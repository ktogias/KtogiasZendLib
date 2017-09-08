<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasZendLib\Application\User\InputFilter;

use KtogiasZendLib\InputFilter\DbTableModelAwareInputFilter;
use Zend\InputFilter\Factory as InputFactory;
use KtogiasZendLib\Model\DbTableModelInterface;

/**
 * Description of UserLdapInputFilter
 *
 * @author ktogias
 */
class UserLdapInputFilter extends DbTableModelAwareInputFilter{
    /**
     * @var Zend\InputFilter\Factory 
     */
    protected $factory;
    
    public function __construct(DbTableModelInterface $model) {
        parent::__construct($model);
        $this->factory = new InputFactory();
        $this->add($this->getUserIdInput());
        $this->add($this->getCnInput());
        $this->add($this->getDnInput());
        $this->add($this->getEmployeeidInput());
        $this->add($this->getUidInput());
        $this->add($this->getMailInput());
         $this->add($this->getTitleInput());
        $this->add($this->getProgramInput());
        $this->add($this->getCreatedAtInput());
        $this->add($this->getUpdatedAtInput());
    }
    
    public function getUserIdInput() {
        return $this->factory->createInput([
            'name' => 'user_id',
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
                ],
            ],
        ]);
    }
    
    public function getCnInput() {
        return $this->factory->createInput([
            'name' => 'cn',
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
    
    public function getDnInput() {
        return $this->factory->createInput([
            'name' => 'dn',
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
    
    public function getEmployeeidInput() {
        return $this->factory->createInput([
            'name' => 'employeeid',
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
    
    public function getUidInput() {
        return $this->factory->createInput([
            'name' => 'uid',
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
    
    public function getMailInput() {
        return $this->factory->createInput([
            'name' => 'mail',
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
                [
                    'name' => 'Regex',
                    'options' => [
                       'pattern' => '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/'
                    ]
                ],
            ],
        ]);
    }
    
    public function getCreatedAtInput() {
        return $this->factory->createInput([
            'name' => 'created_at',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'Date',
                    'options' => [
                        'format' => 'Y-m-d H:i:s'
                    ]
                ],
            ],
        ]);
    }
    
    public function getUpdatedAtInput() {
        return $this->factory->createInput([
            'name' => 'updated_at',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'Date',
                    'options' => [
                        'format' => 'Y-m-d H:i:s'
                    ]
                ],
            ],
        ]);
    }
    
    public function getProgramInput() {
        return $this->factory->createInput([
            'name' => 'program',
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
                        'max'      => 255,
                    ],
                ],
            ],
        ]);
    }
    
    public function getTitleInput() {
        return $this->factory->createInput([
            'name' => 'title',
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
}
