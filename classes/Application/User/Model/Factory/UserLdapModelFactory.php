<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\User\Model\Factory;

use KtogiasZendLib\Model\Factory\ValidatingDbTableModelFactory;
use Zend\ServiceManager\FactoryInterface;

/**
 * Description of UserLdapModelFactory
 *
 * @author ktogias
 */
class UserLdapModelFactory extends ValidatingDbTableModelFactory implements FactoryInterface{
    protected $tableClass = 'KtogiasZendLib\Application\User\Db\Table\UserLdapTable';
    protected $inputFilterClass = 'KtogiasZendLib\Application\User\InputFilter\UserLdapInputFilter';
}
