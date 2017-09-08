<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\InputFilter;

use Zend\InputFilter\InputFilter;  
use KtogiasZendLib\Model\UploadedFileModelInterface;
use Zend\InputFilter\Factory as InputFactory;


/**
 * Description of UploadedFileModelAwareInputFilter
 *
 * @author ktogias
 */
abstract class UploadedFileModelAwareInputFilter extends InputFilter implements UploadedFileModelAwareInputFilterInterface {
    /**
     *
     * @var \KtogiasZendLib\Model\UploadedFileModelInterface
     */
    protected $model;
    
    /**
     * @var Zend\InputFilter\Factory 
     */
    protected $factory;
    
    /**
     * 
     * @param \KtogiasZendLib\Model\UploadedFileModelInterface $model
     */
    public function __construct(UploadedFileModelInterface $model) {
        $this->model = $model;
        $this->factory = new InputFactory();
        $this->add($this->getFileInput());
    }
    
    /**
     * 
     * @return KtogiasZendLib\Model\UploadedFileModelInterface
     */
    public function getModel() {
        return $this->model;
    }
    
    protected function getFileInput(){
        return $this->factory->createInput([
                'name'     => 'file',
                'attributes' => ['type' => 'file'],
                'required' => true,
                /*'filters'  => [
                    [
                        'name' => 'Zend\Filter\File\RenameUpload',
                        'options' => [
                            'target' => 'tmp/test.pdf',
                            'overwrite' => false,
                        ],
                    ],
                ],*/
                'validators' => [
                    [
                        'name'    => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => $this->getModel()->getExtensions(),
                        ],
                    ],
                    [
                        'name'    => 'Zend\Validator\File\MimeType',
                        'options' => [
                            'mimeType' => $this->getModel()->getMimeTypes(),
                        ],
                    ],
                    [
                        'name'    => 'Zend\Validator\File\Size',
                        'options' => [
                            'max' => $this->getModel()->getMaxSize(),
                        ],
                    ],
                ],
            ]);
    }

    
}
