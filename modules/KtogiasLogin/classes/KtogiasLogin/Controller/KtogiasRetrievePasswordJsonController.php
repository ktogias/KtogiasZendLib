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
use Zend\Mail;

/**
 * Description of KtogiasRetrievePasswordJsonController
 *
 * @author ktogias
 */
class KtogiasRetrievePasswordJsonController extends Controller implements JsonControllerInterface, LoggingAwareInterface {
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface 
     */
    protected $logModel;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->setLogModel($e->getApplication()->getServiceManager()->get('KtogiasZendLib\Application\Log\Model\LogModel'));
        parent::onDispatch($e);
    }
    
    public function retrieveAction(){
        if ($this->getRequest()->isPost()){
            $data = \Zend\Json\Json::decode($this->getRequest()->getContent());
            $auth = new AuthenticationService();
            $auth->clearIdentity();
            $user = clone $this->getServiceLocator()->get('KtogiasLogin\Model\UserModel');
            /*@var $user \KtogiasLogin\Model\UserModelInterface*/          
            try {
                $user->loadByUsernameOrEmail($data->usernameOrEmail);
                if ($user->isLdap()){
                    $user->updateFromLdapByMail();
                }
            } catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e) {
                try {
                    $user->loadUpdateCreateFromLdapByMailOrUid($data->usernameOrEmail); 
                } catch (\KtogiasZendLib\Application\User\Model\Exception\LdapUserNotFoundException $ex) {
                    $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController', 'retrieveAction', NULL, NULL, 'Ldap user not found for '.$data->usernameOrEmail);
                    return [
                        'emailSent' => false,
                        'messages' => ['Λάθος όνομα χρήστη ή email. Ο λογαριασμός δεν βρέθηκε.'],
                    ];
                } catch (\KtogiasZendLib\Application\User\Model\Exception\RoleByLdapTitleNotFoundException $ex) {
                    $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController', 'retrieveAction', NULL, NULL, 'Role by Ldap ttile not found. User not found for '.$data->usernameOrEmail);
                    return [
                        'emailSent' => false,
                        'messages' => ['Λάθος όνομα χρήστη ή email. Ο λογαριασμός δεν βρέθηκε.'],
                    ];
                }
            }
            catch (\KtogiasZendLib\Application\User\Model\Exception\RoleByLdapTitleNotFoundException $ex) {
                $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController', 'retrieveAction', NULL, NULL, 'Role by Ldap ttile not found. User not found for '.$data->usernameOrEmail);
                return [
                    'emailSent' => false,
                    'messages' => ['Λάθος όνομα χρήστη ή email. Ο λογαριασμός δεν βρέθηκε.'],
                ];
            }
            $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->beginTransaction();
            try {
                if (!$user->isActive()){
                    throw new Exception\UserNotActiveException();
                }
                list($token, $encryptedToken) = $user->generateToken($user);
                $this->sendRetrievalEmail($user, $token, $data->lang);
                $user->setLoginToken($encryptedToken)->save();
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->commit();
                $this->logModel->log('info', 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController', 'retrieveAction', NULL, NULL, 'Password retrieval email sent to '.$user->getEmail());
                return [
                    'emailSent' => true,
                    'messages' => [
                        'Ένα μήνυμα με οδηγίες για την ανάκτηση του κωδικού σας έχει αποσταλεί στο email σας:',
                        $user->getEmail(),
                        'Ακολουθήστε τις οδηγίες που αναγράφονται σε αυτό προκειμένου να ανακτήσετε τον κωδικό σας.',
                        'Σε περίπτωση που δεν λάβετε το email με τις οδηγίες μέσα στα επόμενα 20 λεπτά, παρακαλούμε επικοινωνήστε με τους διαχειριστές της εφαρμογής.',
                    ]
                ];
            }
            catch(Exception\UserNotActiveException $e){
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                $this->logModel->log('warning', 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController', 'retrieveAction', NULL, NULL, 'User not active for '.$data->usernameOrEmail);
                return [
                    'emailSent' => false,
                    'messages' => ['Ο λογαριασμός δεν είναι ενεργός.'],
                ];
            }
            catch(\Exception $e){
                $user->getTable()->getTableGateway()->getAdapter()->getDriver()->getConnection()->rollback();
                $this->logModel->log('error', 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController', 'retrieveAction', NULL, NULL, 'Application error while sending email for '.$data->usernameOrEmail.' ('.$user->getEmail().')', [
                    'exception'=> get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                    ]);
                return [
                    'emailSent' => false,
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

    /**
     * 
     * @return LogModelInterface
     */
    public function getLogModel() {
        return $this->logModel;
    }

    /**
     * 
     * @param LogModelInterface $logModel
     */
    public function setLogModel(LogModelInterface $logModel) {
        $this->logModel = $logModel;
    }
    
    /**
     * 
     * @param \KtogiasLogin\Model\UserModelInterface $user
     * @param string $token
     * @param string $lang
     * 
     * @todo Get email text and subject from config!
     */
    protected function sendRetrievalEmail(\KtogiasLogin\Model\UserModelInterface $user, $token, $lang){
        $config = $this->serviceLocator->get('config');
        $emailConfig = $config['ktogias-login']['retrieve-password-email'];
        $mail = new Mail\Message();
        $mail->setEncoding('UTF-8');
        $mail->setBody($this->templateString($emailConfig['body'][$lang], [
            'name' => $user->getFullname(),
            'url' => ($this->getRequest()->getServer('HTTPS')?"https://":"http://")
                .$this->getRequest()->getServer('HTTP_HOST')
                ."/login/password-reset/token/".$token
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
