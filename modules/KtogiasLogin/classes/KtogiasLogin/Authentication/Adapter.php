<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasLogin\Authentication;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Adapter implements AdapterInterface,  ServiceLocatorAwareInterface{
    
    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    private $servicelocator;
    
    private $username;
    
    private $password;
    
    /**
     * @return \Zend\Authentication\Result
     */
    public function authenticate() {
        $user = clone $this->getServiceLocator()->get('KtogiasLogin\Model\UserModel');
        /*@var $user \KtogiasLogin\Model\UserModelInterface*/
        $log = clone $this->getServiceLocator()->get('KtogiasZendLib\Application\Log\Model\LogModel');
        /*@var $log \KtogiasZendLib\Application\Log\Model\LogModel*/
        try {
            $user->loadByUsernameOrEmail($this->username);
            if ($user->isLdap()){
                $user->updateFromLdapByMail();
            }
        } catch (\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $ex) {
            try {
                $user->loadUpdateCreateFromLdapByMailOrUid($this->username);
            } catch (\KtogiasZendLib\Application\User\Model\Exception\LdapUserNotFoundException $ex) {
                $log->log('debug', get_class($this), 'authenticate', NULL, NULL, 'LdapUserNotFoundException', ['message' => $ex->getMessage(), 'trace' => $ex->getTrace(), 'class' => get_class($ex), 'user' => $user->getArrayCopy()]);
                return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE, []);
            } catch (\KtogiasZendLib\Application\User\Model\Exception\RoleByLdapTitleNotFoundException $ex) {
                $log->log('debug', get_class($this), 'authenticate', NULL, NULL, 'RoleByLdapTitleNotFoundException', ['message' => $ex->getMessage(), 'trace' => $ex->getTrace(), 'class' => get_class($ex), 'user' => $user->getArrayCopy()]);
                return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE, []);
            }
        } catch (\KtogiasZendLib\Application\User\Model\Exception\RoleByLdapTitleNotFoundException $ex) {
            $log->log('debug', get_class($this), 'authenticate', NULL, NULL, 'RoleByLdapTitleNotFoundException', ['message' => $ex->getMessage(), 'trace' => $ex->getTrace(), 'class' => get_class($ex), 'user' => $user->getArrayCopy()]);
            return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE, []);
        }
        try {
          if ($user->isActive() && $user->verifyPassword($this->password)){
              return new \Zend\Authentication\Result(\Zend\Authentication\Result::SUCCESS, [
                    'user' => [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                    ]
                ]);
          }
          else {
              $log->log('debug', get_class($this), 'authenticate', NULL, NULL, 'Not active or wrong password', ['user' => $user->getArrayCopy()]);
              return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE, []);
          }
        } 
        catch(\KtogiasZendLib\Db\Table\Exception\DbTableNoResultException $e){
            $log->log('debug', get_class($this), 'authenticate', NULL, NULL, 'RoleByLdapTitleNotFoundException', ['message' => $e->getMessage(), 'trace' => $e->getTrace(), 'class' => get_class($e), 'user' => $user->getArrayCopy()]);
            return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE, []);
        } 
    }

    public function getServiceLocator() {
        return $this->servicelocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->servicelocator = $serviceLocator;
    }

    public function setCredentials($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

}
