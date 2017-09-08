<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasZendLib\Application\Log\Model;

use KtogiasZendLib\Model\ValidatingDbTableModel;
use KtogiasZendLib\Authentication\UserAuthenticationServiceInterface;
use KtogiasZendLib\Application\User\Model\UserModelInterface;
/**
 * Description of RoleModel
 *
 * @author ktogias
 */
class LogModel extends ValidatingDbTableModel implements LogModelInterface{
    
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $type;
    
    /**
     *
     * @var string
     */
    protected $datetime;
    
    /**
     *
     * @var integer
     */
    protected $user_id;

    /**
     *
     * @var string
     */
    protected $resource;
    
    /**
     *
     * @var string
     */
    protected $privilege;
    
    /**
     *
     * @var string
     */
    protected $access;
    
    /**
     *
     * @var string
     */
    protected $message;
    
    /**
     *
     * @var string
     */
    protected $trace;
    
    /**
     *
     * @var string
     */
    protected $ip;
    
    protected $fields = ['id', 'type', 'datetime', 'user_id', 'resource', 'privilege', 'access', 'message', 'trace', 'ip'];
    
    /**
     *
     * @var \KtogiasZendLib\Application\User\Model\UserModel
     */
    protected $user;
    
    /**
     * @var \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface
     */
    protected $auth;
    
    public function getUser() {
        
    }

    /**
     * 
     * @param string $type one of 'debug', 'info', 'warning', 'error'
     * @param mixed $resource string, ResourceInterface, ResourceAwareInterface
     * @param string $privilege
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $user
     * @param string $access one of 'allow', 'deny', NULL
     * @param string $message
     * @param array $trace
     * @return type
     */
    public function log($type, $resource, $privilege, UserModelInterface $user = NULL, $access = NULL, $message = NULL, array $trace = NULL) {

        if (($type == 'debug') && array_key_exists('APPLICATION_ENV', $_SERVER) && $_SERVER['APPLICATION_ENV'] != 'development') {
            return;
        }
        if (is_string($resource)){
            $resourceId = $resource;
        }
        else if (is_subclass_of($resource, 'Zend\Permissions\Acl\Resource\ResourceInterface')){
            $resourceId = $resource->getResourceId();
        }
        else if (is_subclass_of($resource, 'KtogiasZendLib\Permissions\Acl\Resource\ResourceAwareInterface')){
            $resourceId = $resource->getResource()->getResourceId();
        }
        
        if ($user){
            $userId = $user->getId();
        }
        else if ($this->auth && $this->auth->hasIdentity() && $this->auth->getUser() != null){
            $userId = $this->auth->getUser()->getId();
        }
        else {
           $userId = NULL; 
        }
        
        $date = new \DateTime();
        
        $this->set([
            'type' => $type,
            'datetime' => $date->format('Y-m-d H:i:s'),
            'user_id' => $userId,
            'resource' => $resourceId,
            'privilege' => $privilege,
            'access' => $access,
            'message' => $message,
            'trace' => $trace == NULL?NULL:json_encode($trace),
            'ip' => $this->serviceLocator->get('Request')->getServer('REMOTE_ADDR')
        ])->save();
    }

    public function getUserAuthenticationService() {
        return $this->auth;
    }

    public function setUserAuthenticationService(UserAuthenticationServiceInterface $auth) {
        $this->auth = $auth;
    }

    public function setNoUserAuthenticationService() {
        $this->auth = null;
    }

}
