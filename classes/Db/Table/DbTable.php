<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Db\Table;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

/**
 * Description of DbTable
 *
 * @author ktogias
 */
abstract class DbTable implements DbTableInterface{
    /**
     *
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;
    /**
     *
     * @var string
     */
    protected $primaryKey;
    
    public function __construct(TableGateway $tableGateway, $primaryKey)
    {
        $this->tableGateway = $tableGateway;
        $this->primaryKey = $primaryKey;
    }
    
    /**
     * 
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }
    
    /**
     * 
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateway(){
        return $this->tableGateway;
    }

    /**
     * 
     */
    public function emptyTable() {
        $this->tableGateway->getAdapter()->query('TRUNCATE TABLE '.$this->tableGateway->table)->execute();
    }

    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @param boolean $mustExist
     * @return \Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll($select = [], $mustExist = false) {
        $resultSet = $this->getResultSet($select);
        if ($mustExist && $resultSet->count() == 0){
            throw new Exception\DbTableNoResultException("No result found while at least one expected");
        }
        $resultSet->buffer();
        return $resultSet;
    }
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @param boolean $mustExist
     * @param boolean $mustUnique
     * @return \KtogiasZendLib\Model\DbTableModelInterface
     * @throws Exception\DbTableMultipleResultsException
     * @throws Exception\DbTableNoResultException
     */
    public function fetchOne($select = [], $mustExist = false, $mustUnique = false) {
        $resultSet = $this->getResultSet($select);
        if ($mustUnique && $resultSet->count() > 1){
            throw new Exception\DbTableMultipleResultsException("Multiple results found while unique result expected");
        }
        $result = $resultSet->current();
        if ($mustExist && !$result) {
            throw new Exception\DbTableNoResultException("No result found while at least one expected");
        }
        return $result;
    }
    
    /**
     * 
     * @param $id
     * @param boolean $mustExist
     * @return \KtogiasZendLib\Model\DbTableModelInterface
     */
    public function fetchById($id, $mustExist = false){
        return $this->fetchOne($this->getWhereById($id), $mustExist, true);
    }
    
    
    

    /**
     * 
     * @param array $data
     * @throws \Exception
     */
    public function insertRows(array $data) {
        $connection = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        try {
            $connection->beginTransaction();
            $this->emptyTable();
            foreach ($data as $row){
                $this->insertRow($row);
            }
            $connection->commit();
        }
        catch (\Exception $ex){
            $connection->rollback();
            throw $ex;
        }
    }

    /**
     * 
     * @param array $data
     * @return int|string
     * @throws \Exception
     */
    public function insertRow(array $data) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $new_id =  $sql->prepareStatementForSqlObject(
            $sql->insert()->into($this->tableGateway->table)->values($data)
        )->execute()->getGeneratedValue();
        return $new_id; 
    }
    
    /**
     * 
     * @param $id
     * @param array $data
     */
    public function updateRow($id, array $data){
        $this->fetchById($id, true);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $affectedRows = $sql->prepareStatementForSqlObject(
            $sql->update()->table($this->tableGateway->table)->set($data)->where($this->getWhereById($id))
        )->execute()->getAffectedRows();
        return $affectedRows;
    }
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @return \Zend\Db\ResultSet\ResultSetInterface or null
     */
    private function getResultSet($select = []){
        try {
            return $this->doSelect($select);
        } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $ex) {
            if ($ex->getPrevious() && $ex->getPrevious()->getCode() == 'HY000'){
                $this->tableGateway->getAdapter()->getDriver()->getConnection()->disconnect();
                $this->tableGateway->getAdapter()->getDriver()->getConnection()->connect();
                return $this->doSelect($select);
            }
            throw $ex;
        }
    }
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @return \Zend\Db\ResultSet\ResultSetInterface or null
     */
    private function doSelect($select){
        if (is_array($select)){
            return $this->tableGateway->select($select);
        }
        else if (is_a($select, '\Zend\Db\Sql\Select')){
            return $this->tableGateway->selectWith($select);
        }
    }

    /**
     * 
     * @param $id
     */
    public function deleteById($id){
        $this->fetchById($id, true);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $affectedRows = $sql->prepareStatementForSqlObject(
            $sql->delete()->from($this->tableGateway->table)->where($this->getWhereById($id))
        )->execute()->getAffectedRows();
        return $affectedRows;
    }
    
    public function countAll($select = []) {
        return $this->getCount($select);
    }
    
    /**
     * 
     * @param \Zend\Db\Sql\Select or array $select
     * @return \Zend\Db\ResultSet\ResultSetInterface
     */
    private function getCount($select = []){
        $sql = new Sql($this->getTableGateway()->getAdapter());
        if (is_array($select)){
            $where = $select;
            $select = $sql->select();
            $select->from($this->getTableGateway()->table)->where($where);
        }
        $select->columns(['num' => new \Zend\Db\Sql\Expression('COUNT(*)')]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();  
        return $results->current()['num'];
    }
    
    /**
     * 
     * @param string or array $id
     * @return array
     * @throws Exception\DbTableWrongIdTypeException
     * @throws Exception\DbTableEmptyWhereByIdException
     */
    protected function getWhereById($id){
        $where = NULL;
        if (is_array($this->primaryKey)){
            if (!is_array($id)){
                try {
                    $id = json_decode($id, true);
                    if(!is_array($id)){
                        throw new Exception\DbTableWrongIdTypeException('Non array id for array primary key!');
                    }
                } catch (Exception $ex) {
                    throw new Exception\DbTableWrongIdTypeException('Non array id for array primary key!');
                }
            }
            $where = [];
            foreach($this->primaryKey as $field){
                if (!array_key_exists($field, $id)){
                    throw new Exception\DbTableWrongIdTypeException('Primary key field '.$field.' not found!');
                }
                $where[$field] = $id[$field];
            }
        }
        else {
            if (is_array($id)){
                throw new Exception\DbTableWrongIdTypeException('Array id for non array primary key!');
            }
            $where = [$this->primaryKey => $id];
        }
        if (empty($where)){
            throw new Exception\DbTableEmptyWhereByIdException();
        }
        return $where;
    }

}
