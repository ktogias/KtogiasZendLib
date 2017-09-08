<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasAngularUserMenu\Controller;

use KtogiasZendLib\Mvc\Controller\AuthController;
use KtogiasZendLib\Mvc\Controller\JsonControllerInterface;


/**
 * Description of IndexController
 *
 * @author ktogias
 */
class JsonController extends AuthController implements JsonControllerInterface{
    
    protected $resourceId = 'KtogiasAngularUserMenu\Controller\JsonController';
    
    public function indexAction(){
        $menuModel = $this->getServiceLocator()->get('KtogiasAngularUserMenu\Model\UserMenuModel');
        /*@var $menuModel \KtogiasAngularUserMenu\Model\UserMenuModelInterface*/
        $menuItems = $menuModel->getMenuItems();
        $config = $this->getServiceLocator()->get('Config');
        return [
            'menu' => [
                'position' => $config['user-menu']['position']?$config['user-menu']['position']:'right',
                'items' => $menuItems,
            ],
            'templates' => [
                'user-menu' => file_get_contents(__DIR__.'/../../../angularjs/ktogias-angular-user-menu/index/template/user-menu.phtml'),
            ]
        ];
    }
    
    
}