<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;

use KtogiasZendLib\Authentication\UserAuthenticationServiceAwareInterface;

use KtogiasZendLib\Authentication\UserAuthenticationServiceInterface;

use KtogiasZendLib\Logging\LoggingAwareInterface;

use KtogiasZendLib\Application\Log\Model\LogModelInterface;

/**
 * Description of AuthAwareModel
 *
 * @author ktogias
 */
abstract class AuthAwareLoggingAwareModel extends Model implements ModelInterface, UserAuthenticationServiceAwareInterface, LoggingAwareInterface{
    
    /**
     *
     * @var \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface 
     */
    protected $auth;
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    protected $logModel;
   
    /**
     * 
     * @return \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface 
     */
    public function getUserAuthenticationService() {
        return $this->auth;
    }

    /**
     * 
     * @param \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface $auth
     * @return $this
     */
    public function setUserAuthenticationService(UserAuthenticationServiceInterface $auth) {
        $this->auth = $auth;
        return $this;
    }
    
    /**
     * 
     * @return \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    public function getLogModel() {
        return $this->logModel;
    }

    /**
     * 
     * @param \KtogiasZendLib\Application\Log\Model\LogModelInterface $logModel
     * @return $this
     */
    public function setLogModel(LogModelInterface $logModel) {
        $this->logModel = $logModel;
        return $this;
    }

}
