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
use KtogiasZendLib\Mvc\Controller\JsonControllerInterface;

/**
 * Description of JsonController
 *
 * @author ktogias
 */
class JsonController extends AuthController implements JsonControllerInterface{
    
    protected $resourceId = 'KtogiasAngularAuth\Controller\JsonController';
    
    public function identityAction(){
        $auth = $this->getUserAuthenticationService();
        if (!$auth->hasIdentity()){
            return ['identity' => NULL];
        }
        $user = $auth->getUser();
        $identity = $user->getSafeArrayCopy();
        $identity['roles'] = [];
        foreach ($user->getRolesWithAntecedents() as $role){
            $identity['roles'][] = $role->getAlias();
        }
        return ['identity' => $identity];
    }
    
    public function unauthenticateAction(){
        $auth = $this->getUserAuthenticationService();
        if (!$auth->hasIdentity()){
            return ['unauthenticated' => true];
        }
        $auth->clearIdentity();
        return ['unauthenticated' => true];
    }
}
