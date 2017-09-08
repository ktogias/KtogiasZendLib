<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;

use KtogiasZendLib\Model\DbTableModelInterface;

/**
 * Description of DbTableModel
 *
 * @author ktogias
 */
abstract class DbTableModel extends ReadOnlyDbTableModel implements DbTableModelInterface{
    /**
     * @var Zend\InputFilter\InputFilterInterface 
     */
    protected $inputFilter; 
    
    /**
     *
     * @var \KtogiasZendLib\Db\Table\DbTableInterface
     */
    protected $table;
  
    /**
     * 
     * @param object or array $data
     * @return \KtogiasZendLib\Model\DbTableModelInterface
     */
    public function set($data){
        if (is_object($data)){
            $data = (array)$data;
        }
        $this->exchangeArray($data);
        return $this;
    }
    
    /**
     * @param $mode one of  \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_INSERT_OR_UPDATE, 
     *                      \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_FORCE_INSERT,
     *                      \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_FORCE_UPDATE
     *                      default \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_INSERT_OR_UPDATE
     * @return \KtogiasZendLib\Model\DbTableModel
     */
    public function save($mode = DbTableModelInterface::SAVEMODE_INSERT_OR_UPDATE){
        $existing = $this->getTable()->fetchById($this->getId());
        $data = $this->getArrayCopy();
        if ($existing){
            if($mode == DbTableModelInterface::SAVEMODE_FORCE_INSERT){
                throw new \Exception('A row with same primary key already exists while mode is SAVEMODE_FORCE_INSERT');
            }
            $this->table->updateRow($this->getId(), $data);
            $this->load($this->getId());
        }
        else {
            if($mode == DbTableModelInterface::SAVEMODE_FORCE_UPDATE){
                throw new \Exception('Failed to find existing row while mode is SAVEMODE_FORCE_UPDATE');
            }
            $newId = $this->table->insertRow($data);
            if ($newId){
                $this->load($newId);
            }
            else {
                $this->load($this->getId());
            }
        }
        return $this;
    }    
       
    /**
     * 
     * @throws Exception
     */
    public function delete(){
        $affectedRows = $this->table->deleteById($this->getId());
        if ($affectedRows != 1){
            throw new Exception('Error deleting!');
        }
    }
    
    
}
