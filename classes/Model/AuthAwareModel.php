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

/**
 * Description of AuthAwareModel
 *
 * @author ktogias
 */
abstract class AuthAwareModel extends Model implements ModelInterface, UserAuthenticationServiceAwareInterface{
    
    /**
     *
     * @var \KtogiasZendLib\Authentication\UserAuthenticationServiceInterface 
     */
    protected $auth;
   
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

}
