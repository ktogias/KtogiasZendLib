<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\User\Model;

use KtogiasZendLib\Model\ReadOnlyDbTableModel;
use KtogiasZendLib\Permissions\Acl\Resource\ResourceInterface;

use Zend\Db\Sql\Sql;

use Zend\Ldap\Ldap;

/**
 * Description of UserModel
 *
 * @author ktogias
 */
class UserModel extends ReadOnlyDbTableModel implements UserModelInterface{
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $username;
    
    /**
     *
     * @var string
     */
    protected $email;
    
    /**
     *
     * @var string
     */
    protected $created_at;
    
    /**
     *
     * @var string
     */
    protected $updated_at;
    
    /**
     *
     * @var string
     */
    protected $firstname;

    /**
     *
     * @var string
     */
    protected $lastname;
    
    /**
     *
     * @var integer
     */
    protected $active;
    
    /**
     *
     * @var int
     */
    protected $updated_by;
    
    /**
     *
     * @var string
     */
    protected $password;
    
    protected $salt;
    
    
    protected $fields = ['id', 'username', 'email', 'created_at', 'updated_at', 'updated_by', 'firstname', 'lastname', 'active', 'password', 'salt'];
    
    protected $immutableFields = ['id', 'username'];
    
    protected $identity;
    
    protected $roles;
    
    protected $rolesWithAntecedents;
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    protected $logModel;
    
    /**
     *
     * @var \KtogiasZendLib\Application\User\Model\UserLdapModel 
     */
    protected $userLdap;
    

    public function getRoles() {
        if ($this->roles == null){
            $this->roles = [];
            foreach ($this->getRolesAsResult() as $res){
                $role = clone $this->serviceLocator->get('KtogiasZendLib\Application\Role\Model\RoleModel');
                /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
                $role->exchangeArray($res);
                $this->roles[] = $role;
            }
        }
        return $this->roles;
    }
    
    private function getRolesAsResult(){
        $sql = new Sql($this->table->getTableGateway()->getAdapter());
        $select = $sql->select();
        $select->from('role')
                ->join('user_role', 'user_role.role_id = role.id', [])
                ->where->equalTo('user_id', $this->id);
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

    public function isAllowed(ResourceInterface $resource, $privilege) {
        foreach ($this->getRoles() as $role){
            /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
            if ($role->isAllowed($resource, $privilege)){
                return true;
            }
        }
        return false;
    }

    /**
     * @todo Implement different behaviour if we get our identity from external provider and if our db is the provider.
     * 
     * @param type $identity
     * @throws Exception\IdentityAlreadySet
     * @throws \KtogiasZendLib\Db\Table\Exception\DbTableNoResultException
     */
    public function setIdentity($identity){
        if (!empty($this->identity)){
            throw new Exception\IdentityAlreadySet();
        }
        $this->validateIdentity($identity);
        $this->identity = $identity;
        try {
            $this->loadByIdentity();
            $this->update();
        }
        catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e){
            //$this->create();
            throw $e;
        }
    }
    
    private function validateIdentity($identity){
        $usernameValidator = new \Zend\Validator\StringLength(['min' => 4, 'max' => 100]);
        $emailValidator = new \Zend\Validator\EmailAddress();
        if (!$usernameValidator->isValid($identity['user']['username'])){
            throw new Exception\InvalidUsernameException();
        }
        if (!$emailValidator->isValid($identity['user']['email'])){
            throw new Exception\InvalidEmailException();
        }
    }
    
    private function loadByIdentity(){
        $modelObject = $this->table->fetchOne(['id' => $this->identity['user']['id']], true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        $this->getLogModel()->log('debug', get_class($this), 'loadByIdentity', $this);
    }
    
    private function update($identityToDb = false){
        if ($this->email !=  $this->identity['user']['email']){
            if ($identityToDb){
                $datetime = new \DateTime();
                $this->email = $this->identity['user']['email'];
                $this->updated_at = $datetime->format('Y-m-d H:i:s');
                $this->save();
            }
            else {
                $this->identity['user']['email'] = $this->email;
            }
        }
        if ($this->username !=  $this->identity['user']['username']){
            if ($identityToDb){
                $datetime = new \DateTime();
                $this->username = $this->identity['user']['username'];
                $this->updated_at = $datetime->format('Y-m-d H:i:s');
                $this->save();
            }
            else {
                $this->identity['user']['username'] = $this->username;
            }
        }
    }
    
    /*private function create(){
        $datetime = new \DateTime();
        $data = [
            'email' => $this->identity['user']['email'], 
            'username' => $this->identity['user']['username'],
            'created_at' => $datetime->format('Y-m-d H:i:s')
        ];
        $this->exchangeArray($data);
        $this->id = $this->table->insertRow($this->getArrayCopy());
        $this->getLogModel()->log('info', get_class($this), 'create', $this);
    }*/

    /**
     * 
     * @return $this
     */
    private function save(){
        $datetime = new \DateTime();
        $this->updated_at = $datetime->format('Y-m-d H:i:s');
        $this->table->updateRow($this->{$this->table->getPrimaryKey()}, $this->getArrayCopy());
        $this->getLogModel()->log('info', get_class($this), 'save', $this);
        return $this;
    }
    
    /**
     * 
     * @return \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    public function getLogModel() {
        if (!$this->logModel){
            $this->setLogModel(clone $this->serviceLocator->get('KtogiasZendLib\Application\Log\Model\LogModel'));
        }
        return $this->logModel;
    }
    
    public function setLogModel(\KtogiasZendLib\Application\Log\Model\LogModelInterface $logModel) {
        $this->logModel = $logModel;
    }

    /**
     * @return array of role_id => \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getRolesWithAntecedents() {
        if ($this->rolesWithAntecedents == NULL){
            $this->rolesWithAntecedents = [];
            foreach ($this->getRoles() as $role){
                /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
                if (!array_key_exists($role->getId(), $this->rolesWithAntecedents)){
                    $this->rolesWithAntecedents[$role->getId()] = $role;
                }
                $parentRoles = $role->getAntecedents();
                foreach ($parentRoles as $prole){
                    /*@var $prole \KtogiasZendLib\Application\Role\Model\RoleModel*/
                    if (!array_key_exists($prole->getId(), $this->rolesWithAntecedents)){
                        $this->rolesWithAntecedents[$prole->getId()] = $prole;
                    }
                }
            }
        }
        return $this->rolesWithAntecedents;
    }

    /**
     * @return array
     */
    public function getAllByRole(\KtogiasZendLib\Application\Role\Model\RoleModel $role) {
        $sql = new Sql($this->table->getTableGateway()->getAdapter());
        $select = $sql->select();
        $select->from('user')
                ->join('user_role', 'user_role.user_id = user.id', [])
                ->where->equalTo('role_id', $role->getId());
        $select->group('user.id');
        $statement = $sql->prepareStatementForSqlObject($select);
        $users = [];
        foreach ($statement->execute() as $result){
            $user = clone $this;
            $user->exchangeArray($result);
            $users[] = $user; 
        };
        return $users;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getFullname($reverse = false) {
        if ($reverse){
            return $this->lastname.' '.$this->firstname;
        }
        return $this->firstname.' '.$this->lastname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function getUsername() {
        return $this->username;
    }
    
    /**
     * 
     * @param string $roleAlias
     * @param boolean $withAntecedents default FALSE
     * @return boolean
     */
    public function hasRoleByAlias($roleAlias, $withAntecedents = false){
        $mRole = clone $this->serviceLocator->get('KtogiasZendLib\Application\Role\Model\RoleModel');
        /*@var $mRole \KtogiasZendLib\Application\Role\Model\RoleModelInterface*/
        $mRole->loadByAlias($roleAlias);
        return $this->hasRole($mRole, $withAntecedents);
    }
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModelInterface $role
     * @param boolean $withAntecedents default FALSE
     * @return boolean
     */
    public function hasRole(\KtogiasZendLib\Application\Role\Model\RoleModelInterface $role, $withAntecedents = false){
        if ($withAntecedents){
            $roles = $this->getRolesWithAntecedents();
        }
        else {
            $roles = $this->getRoles();
        }
        foreach ($roles as $arole){
            /*@var $arole \KtogiasZendLib\Application\Role\Model\RoleModelInterface*/
            if ($arole->getId() == $role->getId()){
                return true;
            }
        }
        return false;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isActive(){
        return $this->active == '1'?true:false;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasPassword(){
        return $this->password?true:false;
    }
    
    /**
     * 
     * @return array
     */
    public function getSafeArrayCopy(){
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ];
    }
    
    /**
     * 
     * @param string $username
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @return $this
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidEmailException
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidUsernameException
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidFirstnameException
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidLastnameException
     */
    public function create($username, $email, $firstname, $lastname){
        $emailValidator = new \Zend\Validator\EmailAddress();
        $emailStrLenValidator = new \Zend\Validator\StringLength(array('min' => 1, 'max' => 100));
        $emailRegexValidator = new \Zend\Validator\Regex(['pattern' => '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/']);
        if (!$emailValidator->isValid($email) || !$emailRegexValidator->isValid($email) || !$emailStrLenValidator->isValid($email)){
            throw new \KtogiasZendLib\Application\User\Model\Exception\InvalidEmailException();
        }
        $usernameStrLenValidator = new \Zend\Validator\StringLength(array('min' => 1, 'max' => 100));
        $usernameRegexValidator = new \Zend\Validator\Regex(['pattern' => '/^[A-Za-z0-9_\-\.@]+$/']);
        if (!$usernameStrLenValidator->isValid($username) || !$usernameRegexValidator->isValid($username)){
            throw new \KtogiasZendLib\Application\User\Model\Exception\InvalidUsernameException();
        }
        $nameStrLenValidator = new \Zend\Validator\StringLength(array('min' => 1, 'max' => 100));
        $nameRegexValidator = new \Zend\Validator\Regex(['pattern' => '/^[a-zA-Zα-ωΑ-Ωά-ώΆ-ΏΐΰΪΫ\s\.\-]+$/']);
        if (!$nameStrLenValidator->isValid($firstname) || !$nameRegexValidator->isValid($firstname)){
            throw new \KtogiasZendLib\Application\User\Model\Exception\InvalidFirstnameException();
        }
        if (!$nameStrLenValidator->isValid($lastname)  || !$nameRegexValidator->isValid($lastname)){
            throw new \KtogiasZendLib\Application\User\Model\Exception\InvalidLastnameException();
        }
        
        $now = new \DateTime();
        $id = $this->table->insertRow([
            'username' => $username, 
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'salt' => md5($username),
            'active' => 0,
            'created_at' => $now->format('Y-m-d H:i:s'),
            'created_by' => NULL
        ]);
        $this->load($id);
        $this->getLogModel()->log('info', get_class($this), 'create', NULL, NULL, 'Created user '.$this->username, ['user' => $this->getArrayCopy()]);
        return $this;
    }
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModelInterface $role
     * @return $this
     * @throws \KtogiasZendLib\Application\User\Model\Exception\UserAlreadyHasThisRoleException
     */
    public function addRole(\KtogiasZendLib\Application\Role\Model\RoleModelInterface $role) {
        if ($this->hasRole($role)){
            throw new \KtogiasZendLib\Application\User\Model\Exception\UserAlreadyHasThisRoleException();
        }
        $sql = new Sql($this->table->getTableGateway()->getAdapter());
        $sql->prepareStatementForSqlObject(
            $sql->insert()->into('user_role')
                ->values(['user_id' => $this->id, 'role_id' => $role->getId()])
        )->execute();
        $this->roles = null;
        $this->getLogModel()->log('info', get_class($this), 'addRole', null, null, 'Added role '.$role->getAlias().' to signed up user '.$this->getUsername(), ['user' => $this->getArrayCopy(), 'role' => $role->getArrayCopy()]);
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
     * @param string $val
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $updater default NULL
     * @return $this
     * @throws Exception\InvalidFirstnameException
     */
    public function updateFirstname($val, UserModelInterface $updater = NULL){
        $nameStrLenValidator = new \Zend\Validator\StringLength(array('min' => 1, 'max' => 100));
        $nameRegexValidator = new \Zend\Validator\Regex(['pattern' => '/^[a-zA-Zα-ωΑ-Ωά-ώΆ-ΏΐΰΪΫ\s\.]+$/']);
        $trim = new \Zend\Filter\StringTrim();
        $firstname = $trim->filter($val);
        if (!$nameStrLenValidator->isValid($firstname) || !$nameRegexValidator->isValid($firstname)){
            throw new Exception\InvalidFirstnameException();
        }
        $this->firstname = $firstname;
        $now = new \DateTime();
        $this->updated_at = $now->format('Y-m-d H:i:s');
        $this->updated_by = $updater?$updater->getId():NULL;
        return $this->save();
    }
    
    /**
     * 
     * @param string $val
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $updater default NULL
     * @return $this
     * @throws Exception\InvalidLastnameException
     */
    public function updateLastname($val, UserModelInterface $updater = NULL){
        $nameStrLenValidator = new \Zend\Validator\StringLength(array('min' => 1, 'max' => 100));
        $nameRegexValidator = new \Zend\Validator\Regex(['pattern' => '/^[a-zA-Zα-ωΑ-Ωά-ώΆ-ΏΐΰΪΫ\s\.]+$/']);
        $trim = new \Zend\Filter\StringTrim();
        $lastname = $trim->filter($val);
        if (!$nameStrLenValidator->isValid($lastname) || !$nameRegexValidator->isValid($lastname)){
            throw new Exception\InvalidLastnameException();
        }
        $this->lastname = $lastname;
        $now = new \DateTime();
        $this->updated_at = $now->format('Y-m-d H:i:s');
        $this->updated_by = $updater?$updater->getId():NULL;
        return $this->save();
    }
    
    /**
     * 
     * @return $this
     */
    public function activate(){
        $this->active = 1;
        return $this->save();
    }
    
    /**
     * 
     * @return $this
     */
    public function deactivate(){
        $this->active = 0;
        return $this->save();
    }
    
    /**
     * 
     * @return \KtogiasZendLib\Application\User\Model\UserLdapModel or NULL
     */
    public function getUserLdap() {
        if ($this->userLdap == NULL){
            try {
                $userLdap = clone $this->serviceLocator->get('KtogiasZendLib\Application\User\Model\UserLdapModel');
                /*@var $userLdap \KtogiasZendLib\Application\User\Model\UserLdapModel*/
            }
            catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $e){
                $this->userLdap = NULL;
                return $this->userLdap;
            }
            try {
                $this->userLdap = $userLdap->load($this->id)->setUser($this);
            }
            catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e){
                $this->userLdap = NULL;
            }
        }
        return $this->userLdap;
    }
    
    /**
     * @return boolean
     */
    public function isLdap() {
        return $this->getUserLdap()?true:false;
    }
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModelInterface $role
     * @return $this
     * @throws \KtogiasZendLib\Application\User\Model\Exception\UserDoesNotHaveThisRoleException
     */
    public function removeRole(\KtogiasZendLib\Application\Role\Model\RoleModelInterface $role) {
        if (!$this->hasRole($role)){
            throw new \KtogiasZendLib\Application\User\Model\Exception\UserDoesNotHaveThisRoleException();
        }
        $sql = new Sql($this->table->getTableGateway()->getAdapter());
        $sql->prepareStatementForSqlObject(
            $sql->delete()->from('user_role')
                ->where(['user_id' => $this->id, 'role_id' => $role->getId()])
        )->execute();
        $this->roles = null;
        $this->getLogModel()->log('info', get_class($this), 'removeRole', null, null, 'Removed role '.$role->getAlias().' from user '.$this->getUsername(), ['user' => $this->getArrayCopy(), 'role' => $role->getArrayCopy()]);
        return $this;
    }
    
    /**
     * 
     * @param string $mail
     * @return $this
     * @throws Exception\LdapUserNotFoundException
     */
    public function loadUpdateCreateFromLdapByMailOrUid($mailOrUid){
        $config = $this->serviceLocator->get('config');
        if (array_key_exists('ldap', $config)){
            $config = $config['ldap'];
            $ldap = new Ldap($config);
            if (strpos($mailOrUid, '@') === FALSE){
                $result = $ldap->search('uid='.$mailOrUid);
            }
            else {
                $result = $ldap->search('mail='.$mailOrUid);
            }
            if ($result->count()){
                 $userLdap = clone $this->serviceLocator->get('KtogiasZendLib\Application\User\Model\UserLdapModel');
                /*@var $userLdap \KtogiasZendLib\Application\User\Model\UserLdapModel*/
                $this->exchangeArray($userLdap->loadUpdateCreateFromLdapData($result->current())->getUser()->getArrayCopy());
                $this->getLogModel()->log('debug', get_class($this), 'loadUpdateCreateFromLdapByMailOrUid', $this);
                return $this;
            }
            else {
                throw new Exception\LdapUserNotFoundException('User with mail or uid ='.$mailOrUid.' not found!');
            }
        }
    }
    
    /**
     * 
     * @return $this
     * @throws Exception\LdapUserNotFoundException
     */
    public function updateFromLdapByMail(){
        if (!$this->isLdap()){
            throw new Exception\UserIsNotLdapException('User with mail='.$this->email.' is not an LDAP user!');
        }
        $config = $this->serviceLocator->get('config')['ldap'];
        $ldap = new Ldap($config);
        $result = $ldap->search('mail='.$this->email);
         if ($result->count()){
            $this->exchangeArray($this->getUserLdap()->updateFromData($result->current())->getUser()->getArrayCopy());
            $this->clearCache();
            return $this;
        }
        else {
            throw new Exception\LdapUserNotFoundException('User with mail='.$this->email.' not found!');
        }
    }
    
    public function clearCache() {
        parent::clearCache();
        $this->roles = NULL;
        $this->rolesWithAntecedents = NULL;
        $this->userLdap = NULL;
        return $this;
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
     * @param String $email
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByEmail($email){
        $select = $this->table->getTableGateway()->getSql()->select();
        $select->where->equalTo('email', $email);
        $modelObject = $this->table->fetchOne($select, true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }

}
