<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasLogin\Model;

use KtogiasZendLib\Application\User\Model\UserModel as KtogiasZendLibUserModel;

use ZxcvbnPhp\Zxcvbn;

use Zend\Ldap\Ldap;

/**
 * Description of UserModel
 *
 * @author ktogias
 */
class UserModel extends KtogiasZendLibUserModel implements UserModelInterface{
    
    protected $salt;
    
    protected $login_token;
    
    protected $login_token_timestamp;
    
    protected $password;
    
    function __construct() {
        $this->fields = array_merge($this->fields, ['salt', 'login_token', 'login_token_timestamp', 'password']);
    }

    /**
     * 
     * @param String $usernameOrEmail
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByUsernameOrEmail($usernameOrEmail){
        $select = $this->table->getTableGateway()->getSql()->select();
        $select->where->equalTo('username', $usernameOrEmail)
                ->or->equalTo('email', $usernameOrEmail);
        $modelObject = $this->table->fetchOne($select, true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }
    
    
    /**
     * 
     * @param String $username
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByUsername($username){
        $select = $this->table->getTableGateway()->getSql()->select();
        $select->where->equalTo('username', $username);
        $modelObject = $this->table->fetchOne($select, true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }
    
    /**
     * 
     * @param string $token
     * @return \KtogiasLogin\Model\UserModel
     */
    public function loadByLoginToken($token){
        $config = $this->serviceLocator->get('config');
        $salt = $config['ktogias-login']['salt'];
        if (!$salt){
            throw new Exception\EmptySaltException('Salt from config is empty!!!');
        }
        $select = $this->table->getTableGateway()->getSql()->select();
        $select->where->equalTo('login_token', new \Zend\Db\Sql\Expression('sha1(concat(?,?,salt))', [$salt, $token]));
        $modelObject = $this->table->fetchOne($select, true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }
    
    /**
     * 
     * @param string $token
     * @return \KtogiasLogin\Model\UserModel
     */
    public function setLoginToken($token){
        $this->login_token = $token;
        $now = new \DateTime();
        $this->login_token_timestamp = $now->format('Y-m-d H:i:s');
        return $this;
    }
    
    /**
     * 
     * @param string $password
     * @return \KtogiasLogin\Model\UserModel
     */
    public function setPassword($password){
        if ($this->isLdap()){
            $this->password = NULL;
            $this->setLdapPassword($password);
        }
        else {
            $this->password = $password;
        }
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getLoginToken(){
        return $this->login_token;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getLoginTokenTimestamp(){
        if ($this->login_token_timestamp){
            return new \DateTime($this->login_token_timestamp);
        }
        else {
            return null;
        }
    }
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     */
    public function save(){
        $now = new \DateTime();
        $this->updated_at = $now->format('Y-m-d H:i:s');
        $this->updated_by = null;
        $primaryKey = $this->table->getPrimaryKey();
        $this->table->updateRow($this->{$primaryKey}, $this->getArrayCopy());
        $this->load($this->{$primaryKey});
        return $this;
    }
    
    /**
     * 
     * @param string $token
     * @return boolean
     */
    public function loginTokenExists($token){
        $select = $this->table->getTableGateway()->getSql()->select();
        $select->where->equalTo('login_token', $token);
        $count = $this->table->countAll($select);
        if ($count > 0){
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param string $token
     * @return string
     * @throws Exception\EmptyModelException
     * @throws Exception\EmptySaltException
     */
    public function encryptToken($token){
        if ($this->isEmpty()){
            throw new Exception\EmptyModelException('encryptToken invocation is not allowed for an empty User model');
        }
        $config = $this->serviceLocator->get('config');
        $salt = $config['ktogias-login']['salt'];
        if (!$salt){
            throw new Exception\EmptySaltException('Salt from config is empty!!!');
        }
        return sha1($salt.$token.$this->salt);
    }
    
    /**
     * Returns the hashed form of the provided password, sutiable to store to db.
     * If the user is LDAP no hashing is performed and the original password is returned.
     * 
     * @param string $password
     * @return string
     * @throws Exception\EmptyModelException
     * @throws Exception\EmptyPasswordException
     * @throws Exception\WeakPasswordException
     * @throws Exception\EmptySaltException
     * @throws Exception\HashMethodNotSupportedException
     */
    public function hashPassword($password){
        if ($this->isEmpty()){
            throw new Exception\EmptyModelException('hashPassword invocation is not allowed for an empty User model');
        }
        if (empty($password)){
            throw new Exception\EmptyPasswordException('Password is empty!!!');
        }
        $config = $this->serviceLocator->get('config');
        $minAllowedPasswordScore = $config['ktogias-login']['min-allowed-password-score'];
        if ($minAllowedPasswordScore > 0){
            $zxcvbn = new Zxcvbn();
            $strength = $zxcvbn->passwordStrength($password, [$this->username, $this->firstname, $this->lastname, $this->email]);
            if ($strength['score'] < $minAllowedPasswordScore){
                throw new Exception\WeakPasswordException('Password is weak!!!');
            }
        }
        $hashMethod = $config['ktogias-login']['password-hash-method'];
        if ($this->isLdap()){
            return $password;
        }
        else if ($hashMethod == 'password_hash'){
            return password_hash($password, PASSWORD_DEFAULT);
        }
        else if ($hashMethod == 'sha1'){
            $salt = $config['ktogias-login']['salt'];
            if (!$salt){
                throw new Exception\EmptySaltException('Salt from config is empty!!!');
            }
            return sha1($salt.$password.$this->salt);
        }
        else {
            throw new Exception\HashMethodNotSupportedException('Unknown hash method!!!');
        }
    }
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     */
    public function removeLoginToken(){
        $this->login_token = NULL;
        $this->login_token_timestamp = NULL;
        return $this;
    }
    
    /**
     * 
     * @param string $password
     * @return boolean
     */
    public function verifyPassword($password){
        if ($this->isLdap()){
            return $this->verifyLdapPassword($password);
        }
        if (empty($password)){
            return false;
        }
        $config = $this->serviceLocator->get('config');
        $hashMethod = $config['ktogias-login']['password-hash-method'];
        if ($hashMethod == 'password_hash'){
            return password_verify($password, $this->password);
        }
        else if ($hashMethod == 'sha1'){
            $salt = $config['ktogias-login']['salt'];
            if (!$salt){
                throw new Exception\EmptySaltException('Salt from config is empty!!!');
            }
            return $this->password == sha1($salt.$password.$this->salt);
        }
        else {
            throw new Exception\HashMethodNotSupportedException('Unknown hash method!!!');
        }
        
    }
    
    /**
     * 
     * @return array
     * @throws Exception\TokenNotSecureException
     * @throws Exception\TokenGenerationMaxTriesReached
     */
    public function generateToken(){
        $times = -1;
        do {
            $times++;
            $a = 0;
            $token = bin2hex(openssl_random_pseudo_bytes(30, $a));
            if (!$a){
                throw new Exception\TokenNotSecureException('Failed to generate a secure token for password retrieval');
            }
            $encryptedToken = $this->encryptToken($token);
        } while ($this->loginTokenExists($encryptedToken) && $times < 100);
        if ($times >= 100){
            throw new Exception\TokenGenerationMaxTriesReached('Failed to generate a unique token for password retrieval');
        }
        return [$token, $encryptedToken];
    }
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     * @throws Exception\EmptyPasswordException
     */
    public function activate(){
        if (!$this->hasPassword()){
            throw new Exception\EmptyPasswordException('User cannot be activated with empty password.');
        }
        $this->active = true;
        return $this;
    }
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     */
    public function deactivate(){
        $this->active = false;
        $this->login_token = null;
        $this->login_token_timestamp = null;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isLoginTokenValid(){
        if (!$this->getLoginToken()){
            return false;
        }
        $config = $this->serviceLocator->get('config');
        $loginTokenExpiry = $config['ktogias-login']['login-token-expiry'];
        $tokenTimestamp = $this->getLoginTokenTimestamp();
        if (!$tokenTimestamp){
            return false;
        }
        $now = new \DateTime();
        if ($tokenTimestamp->add(new \DateInterval($loginTokenExpiry)) < $now){
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @param string $password
     * @return boolean
     */
    public function verifyLdapPassword($password){
        $config = $this->serviceLocator->get('config')['ldap'];
        $ldap = new Ldap($config);
        $result = $ldap->search('mail='.$this->email);
        if ($result->count()){
            try {
                $ldap->bind($result->current()['dn'], $password);
                return true;
            }
            catch (\Zend\Ldap\Exception\LdapException $e){
                return false;
            }
        }
        else {
            throw new Exception\LdapUserNotFoundException('Ldap user with mail='.$this->email.' not found!');
        }
    }
    
    /**
     * 
     * @param string $password
     * @return string
     */
    public function getKeyFromPassword($password) {
        $config = $this->serviceLocator->get('config');
        $salt = $config['ktogias-login']['salt'];
        $iterations = $config['ktogias-login']['iterations'];
        $size = $config['ktogias-login']['keysize'];
        return parent::getKey($password, $salt, $iterations, $size);
    }
    
    /**
     * 
     * @param string $password
     * @return boolean
     */
    protected function setLdapPassword($password){
        $config = $this->serviceLocator->get('config')['ldap'];
        $ldap = new Ldap($config);
        $result = $ldap->search('mail='.$this->email);
        if ($result->count() == 1){
            $ldap->update($result->current()['dn'], ['userpassword' => $password]);
            $ldap->bind($result->current()['dn'], $password);
            return $this;
        }
        else {
            throw new Exception\LdapUserNotFoundException('Ldap user with mail='.$this->email.' not found!');
        }
    }
    
    
    
}
