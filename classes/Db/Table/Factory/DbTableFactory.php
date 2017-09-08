<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Db\Table\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of DbTableFactory
 *
 * @author ktogias
 */
abstract class DbTableFactory implements FactoryInterface{
    
    protected $tableName;
    protected $primaryKey;
    protected $dbAdapterClassName = 'Zend\Db\Adapter\Adapter';
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $tableClass = $this->getTableClass();
        $dbAdapter = $serviceLocator->get($this->dbAdapterClassName);
        $tableGateway = new TableGateway($this->tableName, $dbAdapter, null, new ResultSet());
        $table = new $tableClass($tableGateway, $this->primaryKey);
        return $table;
    }
    
    private function getTableClass(){
        $refClass = new \ReflectionObject($this);
        $stripFactory = substr($refClass->getName(), 0, strrpos($refClass->getName(), 'Factory'));
        return strtr($stripFactory, array('\Factory' => ''));
    }
}
