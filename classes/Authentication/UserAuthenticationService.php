<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Authentication;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of AuthenticationService
 *
 * @author ktogias
 */
class UserAuthenticationService extends \Zend\Authentication\AuthenticationService implements UserAuthenticationServiceInterface {
    /**
     *
     * @var ServiceLocatorInterface 
     */
    private $serviceLocator;
    
    /**
     *
     * @var \KtogiasZendLib\Application\User\Model\UserModel 
     */
    private $user;
    
    public function __construct(ServiceLocatorInterface $serviceLocator, \Zend\Authentication\Storage\StorageInterface $storage = null, \Zend\Authentication\Adapter\AdapterInterface $adapter = null) {
        parent::__construct($storage, $adapter);
        $this->serviceLocator = $serviceLocator;
        if ($this->hasIdentity()){
            $this->user = clone $this->serviceLocator->get('KtogiasZendLib\Authnetication\UserModel'); 
            $this->user->setIdentity($this->getIdentity());
        }
    }
    
    public function getUser(){
        if (!$this->hasIdentity()){
            throw new Exception\NotAuthenticatedException();
        }
        return $this->user;
    }
    
    public function authenticate(\Zend\Authentication\Adapter\AdapterInterface $adapter = null) {
        $result = parent::authenticate($adapter);
        $this->user = clone $this->serviceLocator->get('KtogiasZendLib\Authnetication\UserModel');
        return $result;
    }
    
    public function clearIdentity() {
        parent::clearIdentity();
        $this->user = null;
    }
    
    /**
     * 
     * @return $this
     */
    public function reload(){
        if ($this->hasIdentity()){
            $this->user = clone $this->serviceLocator->get('KtogiasZendLib\Authnetication\UserModel'); 
            $this->user->setIdentity($this->getIdentity());
        }
        else {
            $this->user = null;
        }
        return $this;
    }

}
