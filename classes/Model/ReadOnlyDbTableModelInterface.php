<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;


use KtogiasZendLib\Db\Table\DbTableInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author ktogias
 */
interface ReadOnlyDbTableModelInterface {
    
    /**
     * @return \KtogiasZendLib\Db\Table\DbTableInterface
     */
    public function getTable();
    
    /**
     * 
     * @param array $data
     */
    public function exchangeArray(array $data);
        
    /**
     * @return array
     */
    public function getArrayCopy();
    
    /**
     * 
     * @param \KtogiasZendLib\Db\Table\DbTableInterface $table
     */
    public function setTable(DbTableInterface $table);
    
    /**
     * 
     * @param ServiceLocatorInterface $sl
     */
    public function setServiceLocator(ServiceLocatorInterface $sl);
    
    
    /**
     * 
     * @param $id
     */
    public function load($id);
    
    /**
     * @return string
     */
    public function getId();
    
    /**
     * 
     * @param string $field
     * @return string
     */
    public function getField($field);
    
    
    /**
     * 
     * @return Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator();
    
    /**
     * @return \KtogiasZendLib\Model\ReadOnlyDbTableModelInterface
     */
    public function clearCache();
}
