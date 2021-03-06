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
use KtogiasZendLib\Mvc\Controller\JsonControllerInterface;
use Zend\Authentication\AuthenticationService;
use KtogiasZendLib\Logging\LoggingAwareInterface;
use KtogiasZendLib\Application\Log\Model\LogModelInterface;
use Zend\Session\Container;
use Zend\Mail;

/**
 * Description of KtogiasResetPasswordJsonController
 *
 * @author ktogias
 */
class KtogiasResetPasswordJsonController extends Controller implements JsonControllerInterface, LoggingAwareInterface {
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface 
     */
    protected $logModel;
    
    
    protected $session;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->setLogModel($e->getApplication()->getServiceManager()->get('KtogiasZendLib\Application\Log\Model\LogModel'));
        parent::onDispatch($e);
    }
    
    public function resetAction(){
        $token = null;
        if ($this->getRequest()->isPost()){
            $this->session = new Container('ktogiaslogin');
            if ($this->session->offsetExists('password_reset_token')){
                $token = $this->session->offsetGet('password_reset_token');
            }
            $data = \Zend\Json\Json::decode($this->getRequest()->getContent());
            $auth = new AuthenticationService();
            $auth->clearIdentity();           
            $user = clone $this->getServiceLocator()->get('KtogiasLogin\Model\UserModel');
            /*@var $user \KtogiasLogin\Model\UserModelInterface*/
            $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->beginTransaction();
            try {
                $user->loadByLoginToken($token);
                if ($user->getUsername() != $data->user->username){
                    return [
                        'success' => false,
                        'messages' => ['Λάθος λογαριασμός.'],
                    ];
                }
                if (!$user->isLoginTokenValid()){
                    return [
                        'success' => false,
                        'messages' => ['Ο συνδέσμος έχει λήξει.'],
                    ];
                }
                $user->removeLoginToken()->setPassword($user->hashPassword($data->password))->save();
                $this->sendResetEmail($user, $data->lang);
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->commit();
                $this->session->offsetUnset('password_reset_token');
                $this->logModel->log('info', 'KtogiasLogin\Controller\KtogiasResetPasswordJsonController', 'resetAction', NULL, NULL, 'Successful password reset for '.$user->getUsername().'. Notification email sent to '.$user->getEmail());
                return [
                    'success' => true,
                    'messages' => [
                        'Ο κωδικός σας άλλαξε.',
                    ]
                ];
            }
            catch(\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e){
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasResetPasswordJsonController', 'resetAction', NULL, NULL, 'Account not found for '.$data->user->username);
                return [
                    'success' => false,
                    'messages' => ['Λάθος όνομα χρήστη ή email. Ο λογαριασμός δεν βρέθηκε.'],
                ];
            }
            catch (\KtogiasLogin\Model\Exception\EmptyPasswordException $e){
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasResetPasswordJsonController', 'resetAction', NULL, NULL, 'Empty password provided found for '.$data->user->username);
                return [
                    'success' => false,
                    'messages' => ['Ο κωδικός δεν μπορεί να είναι κενός.'],
                ];
            }
            catch (\KtogiasLogin\Model\Exception\WeakPasswordException $e){
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasResetPasswordJsonController', 'resetAction', NULL, NULL, 'Weak password provided  for '.$data->user->username);
                return [
                    'success' => false,
                    'messages' => ['Ο κωδικός δεν είναι αρκετά ισχυρός.'],
                ];
            }
            catch(\Exception $e){
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                $this->logModel->log('error', 'KtogiasLogin\Controller\KtogiasResetPasswordJsonController', 'resetAction', NULL, NULL, 'Application error while sending email for '.$data->user->username.' ('.$user->getEmail().')', [
                    'exception'=> get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                    ]);
                return [
                    'success' => false,
                    'messages' => ['Σφάλμα εφαρμογής. Παρακαλούμε προσπαθήστε ξανά.'],
                ];       
            }
            
        }
        else {
            $this->getResponse()->setStatusCode(400);
            return [
                'error' => 'Expected POST',  
            ];
        }
    }

    public function getLogModel() {
        return $this->logModel;
    }

    public function setLogModel(LogModelInterface $logModel) {
        $this->logModel = $logModel;
    }
    
    
    /**
     * 
     * @param \KtogiasLogin\Model\UserModelInterface $user
     * @param string $lang
     * 
     * @todo Get email text and subject from config!
     */
    protected function sendResetEmail(\KtogiasLogin\Model\UserModelInterface $user, $lang){
        $config = $this->serviceLocator->get('config');
        $emailConfig = $config['ktogias-login']['reset-password-email'];
        $now = new \DateTime();
        $mail = new Mail\Message();
        $mail->setEncoding('UTF-8');
        $mail->setBody($this->templateString($emailConfig['body'][$lang], [
            'name' => $user->getFullname(),
            'date' => $now->format('d/m/Y H:i:s')
        ]));
        
        $mail->setFrom($emailConfig['from-email'], $emailConfig['from-name'][$lang]);
        $mail->addTo($user->getEmail(), $user->getFullname());
        $mail->setSubject($emailConfig['subject'][$lang]);
        
        $headers = $mail->getHeaders();
        $mail->setHeaders($headers->setEncoding('UTF-8')->addHeaderLine('Content-Type: text/plain;charset=UTF-8'));
        
        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);
    }
    
    protected function templateString($template, $vars){
        $result = $template;
        foreach($vars as $key => $value){
            $result = str_replace('{{'.$key.'}}', $value, $result);
        }
        return $result;
    }
}
