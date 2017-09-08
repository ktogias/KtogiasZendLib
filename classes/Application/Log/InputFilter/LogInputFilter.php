<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasZendLib\Application\Log\InputFilter;

use KtogiasZendLib\InputFilter\DbTableModelAwareInputFilter;
use Zend\InputFilter\Factory as InputFactory;
use KtogiasZendLib\Model\DbTableModelInterface;

/**
 * Description of LogInputFilter
 *
 * @author ktogias
 */
class LogInputFilter extends DbTableModelAwareInputFilter{
    /**
     * @var Zend\InputFilter\Factory 
     */
    protected $factory;
    
    public function __construct(DbTableModelInterface $model) {
        parent::__construct($model);
        $this->factory = new InputFactory();
        $this->add($this->getIdInput());
        $this->add($this->getTypeInput());
        $this->add($this->getDatetimeInput());
        $this->add($this->getUserIdInput());
        $this->add($this->getResourceInput());
        $this->add($this->getPrivilegeInput());
        $this->add($this->getAccessInput());
        $this->add($this->getMessageInput());
        $this->add($this->getTraceInput());
        $this->add($this->getIpInput());
    }
    
    public function getIdInput() {
        return $this->factory->createInput([
            'name' => 'id',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 11,
                    ],
                ],
                [
                    'name'    => 'Digits',
                ],
            ],
        ]);
    }
    
    public function getTypeInput() {
        return $this->factory->createInput([
            'name' => 'type',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => ['debug', 'info', 'warning', 'error'],
                    ],
                ],
            ],
        ]);
    }
    
    public function getDatetimeInput() {
        return $this->factory->createInput([
            'name' => 'datetime',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'Date',
                    'options' => [
                        'format' => 'Y-m-d H:i:s',
                    ],
                ],
            ],
        ]);
    }
    
    public function getUserIdInput() {
        return $this->factory->createInput([
            'name' => 'user_id',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 11,
                    ],
                ],
                [
                    'name'    => 'Digits',
                ],
                [
                    'name'    => 'db\recordExists',
                    'options' => [
                        'table' => 'user',
                        'field' => 'id',
                        'adapter' => $this->model->getTable()->getTableGateway()->getAdapter(),
                    ]
                ]
            ],
        ]);
    }
    
    public function getResourceInput(){
        return $this->factory->createInput([
            'name' => 'resource',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 500,
                    ],
                ],
            ],
        ]);
    }
    
    public function getPrivilegeInput(){
        return $this->factory->createInput([
            'name' => 'privilege',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 255,
                    ],
                ],
            ],
        ]);
    }
    
    public function getAccessInput() {
        return $this->factory->createInput([
            'name' => 'access',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => ['allow', 'deny'],
                    ],
                ],
            ],
        ]);
    }
    
    public function getMessageInput() {
        return $this->factory->createInput([
            'name' => 'message',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);
    }
    
    public function getTraceInput() {
        return $this->factory->createInput([
            'name' => 'trace',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);
    }
    
    public function getIpInput(){
        return $this->factory->createInput([
            'name' => 'ip',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'Ip',
                ],
            ],
        ]);
    }
}
