<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Mvc\Controller;

use KtogiasZendLib\Authentication\UserAuthenticationService;
use KtogiasZendLib\Permissions\Acl\Resource\Resource;
use KtogiasZendLib\Application\Log\Model\LogModelInterface;

/**
 * A controller that self-checks access rights against its resource. 
 * If access is denied action is not executed and 401 or 403 is returned to client.
 *
 * @author ktogias
 */
abstract class AuthController extends Controller implements AuthControllerInterface{
    /**
     *
     * @var \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface 
     */
    protected $auth;
    
    /**
     *
     * @var \Zend\Mvc\MvcEvent 
     */
    protected $dispatchEvent;
    
    /**
     *
     * @var string
     */
    protected $resourceId;
    
    /**
     *
     * @var \KtogiasZendLib\Permissions\Acl\Resource\Resource
     */
    protected $resource;
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    protected $logModel;
    
    public function __construct() {
        if ($this->resourceId == NULL){
            throw new Exception\NoResourceIdException();
        }
    }
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->dispatchEvent = $e;
        $this->auth = new UserAuthenticationService($this->getServiceLocator());
        $this->setLogModel($e->getApplication()->getServiceManager()->get('KtogiasZendLib\Application\Log\Model\LogModel'));
        if (!$this->auth->hasIdentity()){
            $this->getLogModel()->log('warning', $this->getResource(), $this->getPrivilege(), NULL, 'deny', 'Unauthorized Access!');
            $this->sendUnautorizedResponse();
            return;
        }
        else if (!$this->isAllowed()){
            $this->sendForbiddenResponse();
            return;
        }
        else {
            parent::onDispatch($e);
        }
    }
    
    public function getResource() {
        if (!$this->resource){
            $this->resource = new Resource($this->resourceId, $this->getServiceLocator());
        }
        return $this->resource;
    }
    
    /**
     * 
     * @return \KtogiasZendLib\Authentication\UserAuthenticationService 
     */
    public function getUserAuthenticationService(){
        return $this->auth;
    }
    
    public function setLogModel(LogModelInterface $logModel) {
        $this->logModel = $logModel;
    }
    
    /**
     * 
     * @return LogModelInterface
     */
    public function getLogModel() {
        return $this->logModel;
    }
    
    protected function sendUnautorizedResponse(){
        $this->dispatchEvent->stopPropagation();
        $response = $this->getResponse();
        $response->setStatusCode(401);
        $this->dispatchEvent->getRouter()->setRoutes([]);
        $view = $this->dispatchEvent->getViewModel();
        $view->setTemplate('error/layout');
        $errorview = new \Zend\View\Model\ViewModel();
        $errorview->setTemplate('error/401');
        $view->addChild($errorview);
    }
    
    protected function sendForbiddenResponse(){
        $this->dispatchEvent->stopPropagation();
        $response = $this->getResponse();
        $response->setStatusCode(403);
        $this->dispatchEvent->getRouter()->setRoutes([]);
        $view = $this->dispatchEvent->getViewModel();
        $view->setTemplate('error/layout');
        $errorview = new \Zend\View\Model\ViewModel();
        $errorview->setTemplate('error/403');
        $view->addChild($errorview);
    }
    
    protected function isAllowed(){
        $message = [
            'params' => [
                'route' => $this->params()->fromRoute(),
                'query' => $this->params()->fromQuery(),
                'post' => $this->params()->fromPost(),
            ],
            'content' => $this->getRequest()->getContent(),
            'type' => $this->getRequest()->getMethod()
        ];
        return $this->getResource()->isUserAllowed($this->auth->getUser(), $this->getPrivilege(), json_encode($message));
    }
    
    protected function getPrivilege(){
        $action = $this->dispatchEvent->getRouteMatch()->getParam('action');
        $parts = explode('-', $action);
        $privilege = array_shift($parts);
        while ($part = array_shift($parts)){
            $privilege.=ucfirst($part);
        }
        $privilege.='Action';
        return $privilege;
    }
    
    /**
     * 
     * @param \Exception $e
     * @param string $message
     */
    protected function logError(\Exception $e, $message){
        try {
            $trace = debug_backtrace();
            $caller = $trace[1];
            $this->logModel->log('error'
                , isset($caller['class'])?$caller['class']:NULL
                , isset($caller['function'])?$caller['function']:NULL
                , $this->auth->getUser()
                , NULL
                , $message
                , [
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ]);
        }
        catch(\Exception $ex){
            echo '!!!Exception logging failed!!! '.$ex->getMessage();
        }
    }
}
