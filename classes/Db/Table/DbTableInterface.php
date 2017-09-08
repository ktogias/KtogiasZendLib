<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Db\Table;


/**
 *
 * @author ktogias
 */
interface DbTableInterface {
    
    /**
     * 
     * @return string
     */
    public function getPrimaryKey();
    
    /**
     * 
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateway();
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @param boolean $mustExist
     * @return \Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll($select = [], $mustExist = false);
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @param boolean $mustExist
     * @param boolean $mustUnique
     * @return \KtogiasZendLib\Model\DbTableModelInterface
     */
    public function fetchOne($select = [], $mustExist = false, $mustUnique = false);
    
    /**
     * 
     * @param $id
     * @param boolean $mustExist default FALSE
     * @return \KtogiasZendLib\Model\DbTableModelInterface
     */
    public function fetchById($id, $mustExist = false);
    
    /**
     * 
     */
    public function emptyTable();
    
    /**
     * 
     * @param array of arrays $data
     */
    public function insertRows(array $data);
    
    /**
     * 
     * @param array $data
     * @return int|string
     */
    public function insertRow(array $data);
    
    /**
     * 
     * @param $id
     * @param array $data
     */
    public function updateRow($id, array $data);
    
    /**
     * 
     * @param $id
     */
    public function deleteById($id);
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @return integer
     */
    public function countAll($select = []);
}
