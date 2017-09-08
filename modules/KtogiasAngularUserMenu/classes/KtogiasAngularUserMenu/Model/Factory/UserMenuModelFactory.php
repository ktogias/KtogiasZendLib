<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasAngularUserMenu\Model\Factory;

use KtogiasZendLib\Model\Factory\DbTableModelFactory;
use Zend\ServiceManager\FactoryInterface;

/**
 * Description of UserModelFactory
 *
 * @author ktogias
 */
class UserMenuModelFactory extends DbTableModelFactory implements FactoryInterface{
    protected $tableClass = 'KtogiasAngularUserMenu\Db\Table\UserMenuTable';
    protected $inputFilterClass = 'KtogiasAngularUserMenu\InputFilter\UserMenuInputFilter';
}
