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
 *
 * @author ktogias
 */
interface AuthAwareValidatingDbTableModelInterface extends ValidatingDbTableModelInterface{
    
    /**
     * 
     * @return \KtogiasZendLib\Authentication\UserAuthenticationService
     */
    public function getAuth();
    
    public function setAuth(UserAuthenticationService $auth);
}
