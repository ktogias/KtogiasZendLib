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
use Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface;

/**
 * Interface for a resource that ACL restrictions are applied on it.
 * It must provide methods for checking if a user or role is allowed 
 * specific privilege access on it.
 * 
 * @author ktogias
 */
interface ResourceInterface extends ZendResourceInterface{
    /**
     * @return array
     */
    public function getAclArray();
    
    /**
     * @return \Zend\Permissions\Acl\Acl
     */
    //public function getAcl();
    
    /**
     * 
     * @param RoleModel $role
     * @param string $privilege
     * @return boolean
     */
    public function isRoleAllowed(RoleModel $role, $privilege);
    
    /**
     * 
     * @param UserModel $user
     * @param string $privilege
     * @return boolean
     */
    public function isUserAllowed(UserModel $user, $privilege);
}
