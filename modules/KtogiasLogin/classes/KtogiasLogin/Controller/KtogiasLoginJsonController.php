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

/**
 * Description of KtogiasLoginJsonController
 *
 * @author ktogias
 */
class KtogiasLoginJsonController extends Controller implements JsonControllerInterface, LoggingAwareInterface {
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface 
     */
    protected $logModel;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->setLogModel($e->getApplication()->getServiceManager()->get('KtogiasZendLib\Application\Log\Model\LogModel'));
        parent::onDispatch($e);
    }
    
    public function loginAction(){
        if ($this->getRequest()->isPost()){
            $data = \Zend\Json\Json::decode($this->getRequest()->getContent());
            $auth = new AuthenticationService();
            $auth->clearIdentity();
            $authAdapter = $this->getServiceLocator()->get('KtogiasLogin\Authentication\Adapter');
            /*@var $authAdaper \KtogiasLogin\Authentication\AdapterInterface*/
            $authAdapter->setCredentials($data->username, $data->password);
            $result = $auth->authenticate($authAdapter);
            /* @var $result \Zend\Authentication\Result*/
            if ($result->isValid()){
                $this->logModel->log('info', 'KtogiasLogin\Controller\loginAction', 'loginAction', NULL, NULL, 'User '.$data->username.' successfuly authenticated');
                return ['authenticated' => true];
            }
            else {
                $this->logModel->log('warning', 'KtogiasLogin\Controller\loginAction', 'loginAction', NULL, NULL, 'User '.$data->username.' authentication failed!');
                return [
                    'authenicated' => false,
                    'message' => 'Λάθος όνομα χρήστη ή κωδικός πρόσβασης. Προσπαθήστε ξανά.'
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

}
