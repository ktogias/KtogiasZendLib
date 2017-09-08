<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\User\Model\Factory;

use KtogiasZendLib\Model\Factory\ReadOnlyDbTableModelFactory;
use Zend\ServiceManager\FactoryInterface;

/**
 * Description of UserModelFactory
 *
 * @author ktogias
 */
class UserModelFactory extends ReadOnlyDbTableModelFactory implements FactoryInterface{
    protected $tableClass = 'KtogiasZendLib\Application\User\Db\Table\UserTable';
}
