<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\User\Db\Table\Factory;

use KtogiasZendLib\Db\Table\Factory\DbTableFactory;
use Zend\ServiceManager\FactoryInterface;

/**
 * Description of UserLdapTableFactory
 *
 * @author ktogias
 */
class UserLdapTableFactory extends DbTableFactory implements FactoryInterface{
    protected $tableName = 'user_ldap';
    protected $primaryKey = 'user_id';
}
