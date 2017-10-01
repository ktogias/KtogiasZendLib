<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * Description of ValidatingDbTableModel
 *
 * @author ktogias
 */
abstract class ValidatingDbTableModel extends DbTableModel implements ValidatingDbTableModelInterface{
    /**
     * 
     * @return Zend\InputFilter\InputFilterInterface 
     */
    public function getInputFilter() {
        return $this->inputFilter;
    }

    /**
     * 
     * @param InputFilterInterface $inputFilter
     * @return \KtogiasZendLib\Model\ValidatingDbTableModelInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
        return $this;
    }
    
    /**
     * 
     * @param object or array $data
     * @return \KtogiasZendLib\Model\ValidatingDbTableModelInterface
     */
    public function validate($data){
        if (is_object($data)){
            $data = (array)$data;
        }
        if (!$this->getInputFilter()->setData($data)->isValid()){
            throw new Exception\DbTableModelDataValidationException(json_encode($this->getInputFilter()->getMessages()));
        }
        return $this;
    }
    
    /**
     * 
     * @param object or array $data
     * @return \KtogiasZendLib\Model\ValidatingDbTableModelInterface
     */
    public function set($data){
        if (is_object($data)){
            $data = (array)$data;
        }
        $this->validate($data)->exchangeArray($data);
        return $this;
    }
    
    /**
     * 
     * @param object or array $data
     * @return \KtogiasZendLib\Model\ValidatingDbTableModelInterface
     */
    public function merge($data){
        if (is_object($data)){
            $data = (array)$data;
        }
        foreach (array_keys($data) as $field){
            if (!in_array($field, $this->fields)){
                throw new Exception\ValidatingDbTableModelFieldNotExistsException($field);
            }
        }
        $mergedData = array_merge($this->getArrayCopy(), $data);
        $this->validate($mergedData)->exchangeArray($mergedData);
        return $this;
    }

}
