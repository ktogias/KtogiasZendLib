<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\User\Model;

use KtogiasZendLib\Model\ValidatingDbTableModel;

/**
 * Description of UserLdapModel
 *
 * @author ktogias
 */
class UserLdapModel extends ValidatingDbTableModel implements UserLdapModelInterface{
    /**
     *
     * @var integer
     */
    protected $user_id;
    
    /**
     *
     * @var string
     */
    protected $cn;
    
    /**
     *
     * @var string
     */
    protected $dn;
    
    /**
     *
     * @var string
     */
    protected $employeeid;
    
    /**
     *
     * @var string
     */
    protected $mail;
    
    /**
     *
     * @var string
     */
    protected $program;
    
    /**
     *
     * @var string
     */
    protected $uid;
    
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
    protected $title;
    
    
    protected $fields = ['user_id', 'cn', 'dn', 'employeeid', 'mail', 'program', 'uid', 'created_at', 'updated_at', 'title'];
    
    /**
     * @return \KtogiasZendLib\Application\User\Model\UserModel
     */
    protected $user;
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    protected $logModel;
    
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
     * @return string
     */
    public function getCn() {
        return $this->cn;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at?new \DateTime($this->created_at):null;
    }

    /**
     * @return string
     */
    public function getDn() {
        return $this->dn;
    }

    /**
     * @return string
     */
    public function getEmployeeid() {
        return $this->employeeid;
    }

    /**
     * @return string
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getProgram() {
        return $this->program;
    }

    /**
     * @return string
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updated_at?new \DateTime($this->updated_at):null;
    }

    /**
     * 
     * @return integer
     */
    public function getUserId() {
        return $this->user_id;
    }
    
    /**
     * @return \KtogiasZendLib\Application\User\Model\UserModel
     */
    public function getUser(){
        if ($this->user == NULL){
            $user = clone $this->serviceLocator->get('KtogiasZendLib\Application\User\Model\UserModel');
            /*@var $user \KtogiasZendLib\Application\User\Model\UserModel*/
            $this->user = $user->load($this->user_id);
        }
        return $this->user;
    }
    
    /**
     * 
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $user
     * @return $this
     * @throws Exception\WrongUserIdException
     */
    public function setUser(UserModelInterface $user){
        if ($user->getId() != $this->user_id){
            throw new Exception\WrongUserIdException('the id of the provided user is not the same with user_id.');
        }
        $this->user = $user;
        return $this;
    }
    
    /**
     * @param $dn string
     * @return $this
     */
    public function loadByDn($dn){
        $modelObject = $this->table->fetchOne(['dn' => $dn], true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        $this->getLogModel()->log('debug', get_class($this), 'loadByDn', NULL, NULL, $dn);
        return $this;
    }
    
    /**
     * @param $uid string
     * @return $this
     */
    public function loadByUid($uid){
        $modelObject = $this->table->fetchOne(['uid' => $uid], true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        $this->getLogModel()->log('debug', get_class($this), 'loadByUid', NULL, NULL, $uid);
        return $this;
    }
    
    /**
     * 
     * @param array $data
     * @return $this
     * @throws Exception\LdapUidHasChangedException
     */
    public function updateFromData(array $data){
        if (!empty($_SERVER['APPLICATION_ENV']) 
                && $_SERVER['APPLICATION_ENV'] == 'development' 
                && $data['mail'][0] == 'ktogias@math.upatras.gr'){
           $data['title'][0] = 'S';
        }
        if ($this->uid != $data['uid'][0]){
            throw new Exception\LdapUidHasChangedException('Ldap uid has changed!');
        }
        if ($this->cn != $data['cn'][0] 
                || $this->dn != $data['dn']
                || $this->employeeid != $data['employeeid'][0]
                || $this->mail != $data['mail'][0]
                || $this->program != (!empty($data['program'])?$data['program'][0]:NULL)
                || $this->uid != $data['uid'][0]
                || $this->title != $data['title'][0]
        ){
            $this->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->beginTransaction();
            try {
                if ($this->title != $data['title'][0]){
                    $user = $this->getUser();
                    $userRoles = $user->getRoles();
                    $ldapRole = clone $this->serviceLocator->get('KtogiasZendLib\Application\Role\Model\RoleModel');
                    /*@var $ldapRole \KtogiasZendLib\Application\Role\Model\RoleModel*/
                    try {
                        $ldapRole->loadByLdapTitle($data['title'][0]);
                    }
                    catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $ex){
                        throw new Exception\RoleByLdapTitleNotFoundException('Role for title '.$data['title'][0].' not found!');
                    }
                    foreach ($userRoles as $role){
                        /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
                        if ($role->isLdap(TRUE) && $role->getId() != $ldapRole->getId() && !$role->isDescendantOf($ldapRole)){
                            $user->removeRole($role);
                        }
                    }
                    if (!$user->hasRole($ldapRole, true)){
                        $user->addRole($ldapRole);
                    }
                }
                $this->set([
                    'user_id' => $this->user_id,
                    'cn' => $data['cn'][0],
                    'dn' => $data['dn'],
                    'employeeid' => $data['employeeid'][0],
                    'mail' => $data['mail'][0],
                    'program' => !empty($data['program'])?$data['program'][0]:NULL,
                    'uid' => $data['uid'][0],
                    'title' => $data['title'][0],
                    'created_at' => $this->created_at,
                    'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),  
                ])->save();
                $this->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->commit();
            }
            catch (\Exception $e){
                $this->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param array $data
     * @return $this
     * @throws \Exception
     */
    public function createFromData(array $data){
        $user = clone $this->serviceLocator->get('KtogiasZendLib\Application\User\Model\UserModel');
        /*@var $user \KtogiasZendLib\Application\User\Model\UserModel*/
        $role = clone $this->serviceLocator->get('KtogiasZendLib\Application\Role\Model\RoleModel');
        /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
        try {
            $role->loadByLdapTitle($data['title'][0]);
        } catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $ex){
            throw new Exception\RoleByLdapTitleNotFoundException('Role for title '.$data['title'][0].' not found!');
        }
        $this->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->beginTransaction();
        
        try {
            try {
                $user->loadByEmail($data['mail'][0]);
            } catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $ex) {
                $user->create($data['mail'][0], $data['mail'][0], $data['givenname'][0], $data['sn'][0]);
            }
            if (!$user->hasRole($role)){
                $user->addRole($role);
            }
            if (!$user->isActive()){
                $user->activate();
            }
            $this->set([
                'user_id' => $user->getId(),
                'cn' => $data['cn'][0],
                'dn' => $data['dn'],
                'employeeid' => $data['employeeid'][0],
                'mail' => $data['mail'][0],
                'program' => !empty($data['program'])?$data['program'][0]:NULL,
                'uid' => $data['uid'][0],
                'title' => $data['title'][0],
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => NULL,  
            ])->save();
            $this->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->commit();
            return $this->setUser($user);
        }
        catch (\Exception $e){
            $this->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
            throw $e;
        }
    }
    
    /**
     * 
     * @param array $data
     * @return $this
     */
    public function loadUpdateCreateFromLdapData(array $data){
        if (!empty($_SERVER['APPLICATION_ENV']) 
                && $_SERVER['APPLICATION_ENV'] == 'development' 
                && $data['mail'][0] == 'ktogias@math.upatras.gr'){
           $data['title'][0] = 'S';
        }
        try {
            return $this->loadByUid($data['uid'][0])->updateFromData($data);
            
        } catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $ex) {
            return $this->createFromData($data);
        }
    }

}
