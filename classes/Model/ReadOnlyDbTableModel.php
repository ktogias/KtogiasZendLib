<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KtogiasZendLib\Model;

use KtogiasZendLib\Db\Table\DbTableInterface;

/**
 * Description of ReadOnlyDbTableModel
 *
 * @author ktogias
 */
abstract class ReadOnlyDbTableModel implements ReadOnlyDbTableModelInterface{

    /**
     *
     * @var \KtogiasZendLib\Db\Table\DbTableInterface
     */
    protected $table;
    
    /**
     *
     * @var Array 
     */
    protected $fields;
    
    /**
     *
     * @var Array
     */
    protected $immutableFields;
    
    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;
    
    /**
     * 
     * @return Array
     */
    public function getArrayCopy() {
        $copy = [];
        foreach ($this->fields as $field){
            $copy[$field] = $this->{$field};
        }
        return $copy;
    }
    
    /**
     * 
     * @param array $data
     */
    public function exchangeArray(array $data) {
        if (!$this->isEmpty() && !$this->respectsImmutableFields($data)){
            throw new Exception\DbTableModelImmutableFieldChangeException();
        }
        foreach($this->fields as $field){
            if (isset($data[$field])){
                if(is_object($data[$field]) && get_class($data[$field]) == 'DateTime'){
                    $this->{$field} = $data[$field]->format('Y-m-d H:i:s');
                }
                else {
                    $this->{$field} = $data[$field];
                }
            }
            else {
                $this->{$field} = null;
            }
        }
    }
    
    public function isEmpty(){
        foreach($this->fields as $field){
            if ($this->{$field} !== NULL){
                return false;
            }
        }
        return true;
    }
    
    private function respectsImmutableFields(array $data){
        if (!empty($this->immutableFields)){
            foreach($this->immutableFields as $field){
                if ($this->{$field} !== $data[$field]){
                    return false;
                }
            }
        }
        return true;
    } 
    
    /**
     * 
     * @param type $id
     * @return \KtogiasZendLib\Model\ReadOnlyDbTableModelInterface
     */
    public function load($id){
        $modelObject = $this->table->fetchById($id, TRUE);
        $this->exchangeArray($modelObject->getArrayCopy());
        return $this;
    }

    /**
     * 
     * @param \KtogiasZendLib\Db\Table\DbTableInterface $table
     * @return \KtogiasZendLib\Model\ReadOnlyDbTableModelInterface
     */
    public function setTable(DbTableInterface $table) {
        $this->table = $table;
        return $this;
    }
    
    /**
     * 
     * @return scalar or array
     */
    public function getId(){
        if (is_array($this->table->getPrimaryKey())){
            $id = [];
            foreach ($this->table->getPrimaryKey() as $field){
                $id[$field] = $this->{$field};
            }
            return $id;
        }
        else {
            return $this->{$this->table->getPrimaryKey()};
        }
    }
    
    /**
     * 
     * @return \KtogiasZendLib\Db\Table\DbTableInterface
     */
    public function getTable() {
        return $this->table;
    }
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sl
     */
    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
        $this->serviceLocator = $sl;
    }
    
    /**
     * 
     * @param string $field
     * @return string
     */
    public function getField($field) {
        if (!in_array($field, $this->fields)){
            throw new Exception\NotExistentFieldException($field);
        }
        return $this->{$field};
    }
    
    /**
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
    
    /**
     * 
     * @return $this
     */
    public function clearCache() {
        return $this;
    }
}
