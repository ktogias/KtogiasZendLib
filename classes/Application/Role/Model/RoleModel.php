<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasZendLib\Application\Role\Model;

use KtogiasZendLib\Model\ReadOnlyDbTableModel;
use KtogiasZendLib\Permissions\Acl\Resource\ResourceInterface;
use Zend\Db\Sql\Sql;

/**
 * Description of RoleModel
 *
 * @author ktogias
 */
class RoleModel extends ReadOnlyDbTableModel implements RoleModelInterface{
    
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $alias;
    
    /**
     *
     * @var string
     */
    protected $description;
    
    /**
     *
     * @var integer
     */
    protected $parent_role_id;

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
     * @var integer
     */
    protected $abstract;
    
    protected $fields = ['id', 'alias', 'description', 'parent_role_id', 'created_at', 'updated_at', 'abstract'];
    
    protected $immutableFields = ['id', 'alias', 'parent_role_id', 'abstract'];
    
    /**
     *
     * @var \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    protected $parent;
    
    /**
     *
     * @var array 
     */
    protected $antecedents;
    
    /**
     * 
     * @return \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getParent(){
        if ($this->parent == null && $this->parent_role_id){
            $parent = clone $this->serviceLocator->get('KtogiasZendLib\Application\Role\Model\RoleModel');
            $parent->load($this->parent_role_id);
            $this->parent = $parent;
        }
        return $this->parent;
    }
    
    public function hasParent(){
        return !empty($this->parent_role_id);
    }

    public function isAllowed(ResourceInterface $resource, $privilege) {
        return $resource->isRoleAllowed($this, $privilege);
    }
    
    public function getAlias(){
        return $this->alias;
    }

    /**
     * @return array of \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getAntecedents() {
        if ($this->antecedents === NULL){
            $this->antecedents = [];
            $arole = $this;
            while ($arole->hasParent()){
                $arole = $arole->getParent();
                $this->antecedents[] = $arole;
            }
        }
        return $this->antecedents;
    }

    /**
     * 
     * @param string $alias
     * @return \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function loadByAlias($alias) {
        $modelObject = $this->table->fetchOne(['alias' => $alias], true, true);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }
    
    /**
     * @param $excludeAbstract boolean default FALSE
     * @return \Zend\Db\ResultSet\ResultSetInterface of \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getAllRoles($excludeAbstract = FALSE) {
        $select = $this->table->getTableGateway()->getSql()->select();
        if ($excludeAbstract){
            $select->where->equalTo('abstract', 0);
        }
        $select->order('description ASC');
        return $this->table->fetchAll($select);
    }
    
    /**
     * 
     * @param string $title
     * @return $this or NULL
     */
    public function loadByLdapTitle($title){
        $select = $this->table->getTableGateway()->getSql()->select();
        $select->join('ldap_title_role', 'ldap_title_role.role_id = role.id')
                ->where->equalTo('ldap_title_role.ldap_title', $title);
        $modelObject =  $this->table->fetchOne($select, TRUE, TRUE);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }
    
    /**
     * 
     * @param boolean $withAntecedents
     * @return boolean default FALSE
     */
    public function isLdap($withAntecedents = FALSE){
        $sql = new Sql($this->table->getTableGateway()->getAdapter());
        $select = $sql->select();
        $select->from('ldap_title_role')->where->equalTo('role_id', $this->id);
        $result = $this->table->countAll($select) > 0;
        if ($result){
            return TRUE;
        }
        if ($withAntecedents){
            foreach ($this->getAntecedents() as $role){
                /*@var $role \KtogiasZendLib\Application\Role\Model\RoleModel*/
                if ($role->isLdap()){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModel $role
     * @return boolean
     */
    public function isDescendantOf(RoleModel $role){
        foreach ($this->getAntecedents() as $antecedent){
            /*@var $antecedent \KtogiasZendLib\Application\Role\Model\RoleModel*/
            if ($antecedent->getId() == $role->getId()){
                return true;
            }
        }
        return false;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isAbstract(){
        return $this->abstract == 1;
    }

}
