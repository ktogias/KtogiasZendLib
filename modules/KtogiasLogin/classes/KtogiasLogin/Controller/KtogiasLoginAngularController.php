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
use KtogiasZendLib\Mvc\Controller\AngularJsControllerInterface;
use Zend\Session\Container;

/**
 * Description of KtogiasLoginAngularController
 *
 * @author ktogias
 */
class KtogiasLoginAngularController extends Controller implements AngularJsControllerInterface{
    
    private $session;
    
    public function ktogiasLoginAppAction() {
        return [];
    }
    
    public function ktogiasLoginModuleAction() {
        $config = $this->getServiceLocator()->get('config');
        $this->filesDir = $config['data_storage']['tmp_dir'];
        return [
            'templates' => [
                'ktogias-login/login-form.phtml' => 'login-form.phtml',
                'ktogias-login/login-help.phtml' => 'login-help.phtml',
            ],
            'afterLoginRedirect' => $config['ktogias-login']['after-login-redirect']
        ];
    }
    
    public function ktogiasRetrievePasswordModuleAction() {
        return [
            'templates' => [
                'ktogias-retrieve-password/retrieve-password-form.phtml' => 'retrieve-password-form.phtml',
                'ktogias-login/retrieve-password-help.phtml' => 'retrieve-password-help.phtml',
            ],
        ];
    }
    
    public function ktogiasResetPasswordModuleAction(){
        $errorCode = null;
        $userdata = null;
        $token = null;
        $config = $this->serviceLocator->get('config');
        $loginTokenExpiry = $config['ktogias-login']['login-token-expiry'];
        $minAllowedPasswordScore = $config['ktogias-login']['min-allowed-password-score'];
        $this->session = new Container('ktogiaslogin');
        if ($this->session->offsetExists('password_reset_token')){
          $token = $this->session->offsetGet('password_reset_token');
        }
        
        $user = clone $this->getServiceLocator()->get('KtogiasLogin\Model\UserModel');
        /*@var $user \KtogiasLogin\Model\UserModelInterface*/
        
        try {
            $user->loadByLoginToken($token);
            if (!$user->isActive()){
                $errorCode = 'NOT_ACTIVE';
            }
            else {
                $now = new \DateTime();
                $tokenTimestamp = $user->getLoginTokenTimestamp();
                if ($tokenTimestamp->add(new \DateInterval($loginTokenExpiry)) < $now){
                    $errorCode = 'EXPIRED';
                }
                else {
                    $userdata = [
                        'username' => $user->getUsername(),
                        'fullname' => $user->getFullname()
                    ];
                }
            }
        }
        catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e){
            $errorCode = 'NOT_FOUND';
        }
        return [
            'user' => $userdata,
            'error_code' => $errorCode,
            'config' => [
                'min-allowed-password-score' => $minAllowedPasswordScore,
            ],
            'templates' => [
                'ktogias-reset-password/reset-password-form.phtml' => 'reset-password-form.phtml',
            ],
        ];
    }
    
    public function ktogiasActivateAccountModuleAction(){
        $errorCode = null;
        $userdata = null;
        $token = null;
        $config = $this->serviceLocator->get('config');
        $loginTokenExpiry = $config['ktogias-login']['login-token-expiry'];
        $minAllowedPasswordScore = $config['ktogias-login']['min-allowed-password-score'];
        $this->session = new Container('ktogiaslogin');
        if ($this->session->offsetExists('account_activation_token')){
          $token = $this->session->offsetGet('account_activation_token');
        }
        
        $user = clone $this->getServiceLocator()->get('KtogiasLogin\Model\UserModel');
        /*@var $user \KtogiasLogin\Model\UserModelInterface*/
        
        try {
            $user->loadByLoginToken($token);
            if ($user->isActive()){
                $errorCode = 'ACTIVE';
            }
            else {
                $now = new \DateTime();
                $tokenTimestamp = $user->getLoginTokenTimestamp();
                if ($tokenTimestamp->add(new \DateInterval($loginTokenExpiry)) < $now){
                    $errorCode = 'EXPIRED';
                }
                else {
                    $userdata = [
                        'username' => $user->getUsername(),
                        'fullname' => $user->getFullname()
                    ];
                }
            }
        }
        catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e){
            $errorCode = 'NOT_FOUND';
        }
        return [
            'user' => $userdata,
            'error_code' => $errorCode,
            'config' => [
                'min-allowed-password-score' => $minAllowedPasswordScore,
            ],
            'templates' => [
                'ktogias-activate-account/activate-account-form.phtml' => 'activate-account-form.phtml',
            ],
        ];
    }
}
