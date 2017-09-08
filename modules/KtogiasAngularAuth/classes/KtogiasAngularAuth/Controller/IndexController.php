<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasAngularAuth\Controller;

use KtogiasZendLib\Mvc\Controller\AuthController;
use KtogiasZendLib\Mvc\Controller\AngularJsControllerInterface;


/**
 * Description of IndexController
 *
 * @author ktogias
 */
class IndexController extends AuthController implements AngularJsControllerInterface{
    
    protected $resourceId = 'KtogiasAngularAuth\Controller\IndexController';
    
    public function indexAction(){
        $auth = $this->getUserAuthenticationService();
        if (!$auth->hasIdentity()){
            return ['identity' => NULL];
        }
        $user = $auth->getUser();
        $identity = $user->getArrayCopy();
        $identity['roles'] = [];
        foreach ($user->getRolesWithAntecedents() as $role){
            $identity['roles'][] = $role->getAlias();
        }
        return ['identity' => $identity];
    }
    
    
}