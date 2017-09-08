<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasAngularUserMenu\InputFilter;

use KtogiasZendLib\InputFilter\DbTableModelAwareInputFilter;
use Zend\InputFilter\Factory as InputFactory;
use KtogiasZendLib\Model\DbTableModelInterface;

/**
 * Description of LogInputFilter
 *
 * @author ktogias
 */
class UserMenuInputFilter extends DbTableModelAwareInputFilter{
    /**
     * @var Zend\InputFilter\Factory 
     */
    protected $factory;
    
    public function __construct(DbTableModelInterface $model) {
        parent::__construct($model);
        $this->factory = new InputFactory();
        $this->add($this->getIdInput());
        $this->add($this->getRoleIdInput());
        $this->add($this->getLabelInput());
        $this->add($this->getRouteInput());
        $this->add($this->getOrderInput());
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
    
    public function getRoleIdInput() {
        return $this->factory->createInput([
            'name' => 'role_id',
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
                        'table' => 'role',
                        'field' => 'id',
                        'adapter' => $this->model->getTable()->getTableGateway()->getAdapter(),
                    ]
                ]
            ],
        ]);
    }
    
    public function getLabelInput(){
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
    
    public function getRouteInput(){
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
    
    public function getOrderInput() {
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
}
