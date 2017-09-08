<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Permissions\Acl\Resource;

use KtogiasZendLib\Application\Role\Model\RoleModel;
use KtogiasZendLib\Application\User\Model\UserModel;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as AclRole;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use KtogiasZendLib\Logging\LoggingAwareInterface;
use KtogiasZendLib\Application\Log\Model\LogModelInterface;

/**
 * A resource that ACL restrictions are applied on it. 
 * ACL Restrictions are loaded from db
 * Methods for checking if a user or role is allowed specific privileges 
 * on the resource are provided
 *
 * @author ktogias
 */
class Resource implements ResourceInterface, ServiceLocatorAwareInterface, LoggingAwareInterface{
    
    protected $resourceId;
    protected $aclArray;
    protected $acl;
    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $serviceLocator;
    
    /**
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    protected $logModel;
    
    /**
     * 
     * @param type $resourceId
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\NoResourceIdException
     * @throws Exception\NoServiceLocatorException
     */
    public function __construct($resourceId, ServiceLocatorInterface $serviceLocator) {
        if ($resourceId == NULL){
            throw new Exception\NoResourceIdException();
        }
        if ($serviceLocator == NULL){
            throw new Exception\NoServiceLocatorException();
        }
        $this->resourceId = $resourceId;
        $this->serviceLocator = $serviceLocator;
        $this->setLogModel($serviceLocator->get('KtogiasZendLib\Application\Log\Model\LogModel'));
        $this->aclArray = $this->getAclArray();
        $this->acl = new Acl();
        $this->acl->addResource($this);
    }

    /**
     * 
     * @return string
     */
    public function getResourceId() {
        return $this->resourceId;
    }
    
    /**
     * 
     * @return array
     */
    public function getAclArray(){
        if ($this->aclArray === NULL){
            $sql = new Sql($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')); 
            $select = $sql->select();
            $select->from('acl')
                ->columns(['role', 'access', 'privilege'])
                ->where->equalTo('resource', $this->resourceId);
            $statement = $sql->prepareStatementForSqlObject($select);
            $this->aclArray = $this->buildAclArrayFromResult($statement->execute()); 
        }
        return $this->aclArray;
    }
    
    /**
     * 
     * @param RoleModel $role
     * @param type $privilege
     * @return boolean
     */
    public function isRoleAllowed(RoleModel $role, $privilege) {
        if ($role->getId() === 0){
            return true;
        }
        $this->setupAcl($role);
        return $this->acl->isAllowed($role->getAlias(), $this, $privilege);
    }
    
    /**
     * 
     * @param UserModel $user
     * @param string $privilege
     * @param string $logMessage
     * @return boolean
     */
    public function isUserAllowed(UserModel $user, $privilege, $logMessage = null) {
        foreach ($user->getRoles() as $role){
            /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
            if ($this->isRoleAllowed($role, $privilege)){
                $this->getLogModel()->log('debug', $this, $privilege, $user, 'allow', $logMessage);
                return true;
            }
        }
        $this->getLogModel()->log('warning', $this, $privilege, $user, 'deny', 'Access denied! '.($logMessage?$logMessage:''));
        return false;
    }
    
    /**
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    /**
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @return \KtogiasZendLib\Permissions\Acl\Resource\Resource
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
    
    /**
     * 
     * @return \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    public function getLogModel() {
        return $this->logModel;
    }

    /**
     * 
     * @param LogModelInterface $logModel
     */
    public function setLogModel(LogModelInterface $logModel) {
        $this->logModel = $logModel;
    }
    
    /**
     * 
     * @param RoleModel $role
     * @return \KtogiasZendLib\Permissions\Acl\Resource\Resource
     */
    private function setupAcl(RoleModel $role){
        if (!$this->acl->hasRole($role->getAlias())){
            $antecendants = $role->getAntecedents();
            $preAntecendant = null;
            /*@var $preAntecendant AclRole*/
            while ($antecendant = array_pop($antecendants)){
                /*@var $antecendant \KtogiasZendLib\Application\Role\Model\RoleModel*/
                $aclRole = new AclRole($antecendant->getAlias());
                if (!$this->acl->hasRole($aclRole)){
                    $this->acl->addRole($aclRole, $preAntecendant);
                    $this->setAclRolePrivileges($aclRole);
                }
                $preAntecendant = $aclRole;
            }
            $aclRole = new AclRole($role->getAlias());
            if (!$this->acl->hasRole($aclRole)){
                $this->acl->addRole($aclRole, $preAntecendant);
                $this->setAclRolePrivileges($aclRole);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param AclRole $role
     */
    private function setAclRolePrivileges(AclRole $role){
        if (array_key_exists($role->getRoleId(), $this->aclArray)){
            if (array_key_exists('deny', $this->aclArray[$role->getRoleId()])){
                $this->acl->deny($role, $this, $this->getAclPrivileges($this->aclArray[$role->getRoleId()]['deny']));
            }
            if (array_key_exists('allow', $this->aclArray[$role->getRoleId()])){
                $this->acl->allow($role, $this, $this->getAclPrivileges($this->aclArray[$role->getRoleId()]['allow']));
            }
        }
    }
    
    /**
     * 
     * @param type $privileges
     * @return array or string
     */
    private function getAclPrivileges($privileges){
        if(is_string($privileges)){
            if ($privileges == '*'){
                return null;
            }
            else {
                return [$privileges];
            }
        }
        else {
            return $privileges;
        }
    }
    
    /**
     * 
     * @param Result $result
     * @return array
     */
    private function buildAclArrayFromResult(Result $result){
        $aclArray = [];
        foreach ($result as $row){
            if (!array_key_exists($row['role'], $aclArray)){
                $aclArray[$row['role']] = [];
            }
            if (!array_key_exists($row['access'], $aclArray[$row['role']])){
                $aclArray[$row['role']][$row['access']] = [];
            }
            if (trim($row['privilege']) === '*'){
                $aclArray[$row['role']][$row['access']] = '*';
            }
            else if (is_array($aclArray[$row['role']][$row['access']])){
                $aclArray[$row['role']][$row['access']][] = trim($row['privilege']);
            }
        }
        return $aclArray;
    }

    

}
