<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\Log\Model\Factory;

use KtogiasZendLib\Model\Factory\ValidatingDbTableModelFactory;
use Zend\ServiceManager\FactoryInterface;

/**
 * Description of LogModelFactory
 *
 * @author ktogias
 */
class LogModelFactory extends ValidatingDbTableModelFactory implements FactoryInterface{
    protected $tableClass = 'KtogiasZendLib\Application\Log\Db\Table\LogTable';
    protected $inputFilterClass = 'KtogiasZendLib\Application\Log\InputFilter\LogInputFilter';
}
