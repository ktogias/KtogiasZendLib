<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasZendLib\Mvc\Controller;

use KtogiasZendLib\Permissions\Acl\Resource\ResourceAwareInterface;
use KtogiasZendLib\Logging\LoggingAwareInterface;

/**
 * Interface for controllers that have a resource attached. 
 *
 * @author ktogias
 */
interface AuthControllerInterface extends ResourceAwareInterface, LoggingAwareInterface{
    
    
}
