<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\Log\Model;

use KtogiasZendLib\Model\DbTableModelInterface;
use KtogiasZendLib\Authentication\UserAuthenticationServiceAwareInterface;
use KtogiasZendLib\Application\User\Model\UserModelInterface;

/**
 *
 * @author ktogias
 */
interface LogModelInterface extends DbTableModelInterface, UserAuthenticationServiceAwareInterface{
    
    /**
     * @return \KtogiasZendLib\Application\User\Model\UserModel
     */
    public function getUser();
    
    /**
     * 
     * @param string $type
     * @param string $resource
     * @param string $privilege
     * @param UserModelInterface $user
     * @param string $access
     * @param string $message
     * @param array $trace
     */
    public function log($type, $resource, $privilege, UserModelInterface $user = NULL, $access=NULL, $message=NULL, array $trace=NULL);
}
