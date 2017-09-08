<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasLogin\Controller;

use KtogiasZendLib\Mvc\Controller\Controller;
use KtogiasZendLib\Mvc\Controller\HtmlControllerInterface;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;


/**
 * Description of Index
 *
 * @author ktogias
 */
class IndexController extends Controller implements HtmlControllerInterface {
    
    protected $session;
    
    public function indexAction(){
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        return [];
    }
    
    public function passwordResetAction(){
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        $this->session = new Container('ktogiaslogin');
        $token = $this->params('token');
        if ($token){
            $this->session->offsetSet('password_reset_token', $token);
        }
        $this->redirect()->toUrl('/login#/reset-password');
    }
    
    public function accountActivationAction(){
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        $this->session = new Container('ktogiaslogin');
        $token = $this->params('token');
        if ($token){
            $this->session->offsetSet('account_activation_token', $token);
        }
        $this->redirect()->toUrl('/login#/activate-account');
    }


}
