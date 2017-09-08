<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;

use KtogiasZendLib\Authentication\UserAuthenticationService;

/**
 * Description of AuthAwareValidatingDbTableModel
 *
 * @author ktogias
 */
abstract class AuthAwareValidatingDbTableModel extends ValidatingDbTableModel implements AuthAwareValidatingDbTableModelInterface{
   
    /**
     *
     * @var \KtogiasZendLib\Authentication\UserAuthenticationService
     */
    protected $auth;
    
    /**
     * 
     * @return \KtogiasZendLib\Authentication\UserAuthenticationService
     */
    public function getAuth() {
        return $this->auth;
    }
    
    /**
     * 
     * @param UserAuthenticationService $auth
     * @return $this
     */
    public function setAuth(UserAuthenticationService $auth) {
        $this->auth = $auth;
        return $this;
    }

}
