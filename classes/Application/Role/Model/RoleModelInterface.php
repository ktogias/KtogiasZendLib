<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\Role\Model;

use KtogiasZendLib\Model\ReadOnlyDbTableModelInterface;
use KtogiasZendLib\Permissions\Acl\Resource\ResourceInterface;
/**
 *
 * @author ktogias
 */
interface RoleModelInterface extends ReadOnlyDbTableModelInterface{
    /**
     *
     * @param ResourceInterface $resource
     * @param string $privilege
     * @return boolean 
     */
    public function isAllowed(ResourceInterface $resource, $privilege);
    
    /**
     * @return boolean 
     */
    public function hasParent();
    
    /**
     * @return \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getParent();
    
    /**
     * @return array
     */
    public function getAntecedents();
    
    /**
     * @param string $alias Role Alias
     * @return RoleModelInterface
     */
    public function loadByAlias($alias);
    
    /**
     * @return string
     */
    public function getAlias();
    
    /**
     * 
     * @return \Zend\Db\ResultSet\ResultSetInterface of \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getAllRoles();
    
    /**
     * 
     * @param string $title
     * @return $this or NULL
     */
    public function loadByLdapTitle($title);
    
    /**
     * 
     * @param boolean $withAntecedents
     * @return boolean default FALSE
     */
    public function isLdap($withAntecedents = FALSE);
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModel $role
     * @return boolean
     */
    public function isDescendantOf(RoleModel $role);
    
    /**
     * 
     * @return boolean
     */
    public function isAbstract();
    
}
