<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Authentication;

/**
 *
 * @author ktogias
 */
interface UserAuthenticationServiceAwareInterface {
    
    public function setUserAuthenticationService(\KtogiasZendLib\Authentication\UserAuthenticationServiceInterface $auth);
    
    /**
     * @return \KtogiasZendLib\Authentication\UserAuthenticationService
     */
    public function getUserAuthenticationService();
}
