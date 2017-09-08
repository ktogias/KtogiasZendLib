<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasAngularUserMenu\Model;

use KtogiasZendLib\Model\DbTableModelInterface;
use KtogiasZendLib\Authentication\UserAuthenticationServiceAwareInterface;

/**
 *
 * @author ktogias
 */
interface UserMenuModelInterface extends DbTableModelInterface, UserAuthenticationServiceAwareInterface{
    public function getMenuItems();
}
