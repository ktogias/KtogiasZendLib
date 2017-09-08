<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasAngularUserMenu\Model;

use KtogiasZendLib\Model\DbTableModel;
use Zend\ServiceManager\ServiceLocatorInterface;
use KtogiasZendLib\Authentication\UserAuthenticationServiceInterface;

/**
 * Description of UserMenu
 *
 * @author ktogias
 */
class UserMenuModel extends DbTableModel implements UserMenuModelInterface{
    
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var integer
     */
    protected $role_id;
    
    /**
     *
     * @var string
     */
    protected $label;
    
    /**
     *
     * @var string
     */
    protected $route;
    
    /**
     *
     * @var integer
     */
    protected $order;
    
    
    protected $fields = ['id', 'role_id', 'label', 'route', 'order'];
    
    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $serviceLovator;
    
    /**
     *
     * @var \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface
     */
    protected $auth;
    
    /**
     *
     * @var type 
     */
    protected $items;
    
    public function setServiceLocator(ServiceLocatorInterface $sl) {
        $this->serviceLovator = $sl;
    }

    /**
     * 
     * @return array of \KtogiasAngularUserMenu\Model\UserMenuModel
     */
    public function getMenuItems() {
        if ($this->items == null){
            $userRolesIds = array_keys($this->auth->getUser()->getRolesWithAntecedents());
            $select = $this->getTable()->getTableGateway()->getSql()->select();
            $select->where
                    ->in('role_id', $userRolesIds);
            $select->order('order ASC, label ASC');
            $this->items = $this->getTable()->fetchAll($select)->toArray();
        }
        return $this->items;
    }

    public function getUserAuthenticationService() {
        return $this->auth;
    }

    public function setUserAuthenticationService(UserAuthenticationServiceInterface $auth) {
        $this->auth = $auth;
    }
}
