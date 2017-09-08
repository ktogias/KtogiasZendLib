<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasLogin\Authentication;

use Zend\Authentication\Adapter\AdapterInterface as ZendAdapterInterface;
/**
 *
 * @author ktogias
 */
interface AdapterInterface extends ZendAdapterInterface{
    /**
     * 
     * @param string $username
     * @param string $password
     */
    public function setCredentials($username, $password);
}
