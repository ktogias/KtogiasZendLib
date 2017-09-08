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
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Filter\FilterChain;

/**
 * Description of UploadedFileModel
 *
 * @author ktogias
 */
abstract class UploadedFileModel implements UploadedFileModelInterface{
    
    /**
     *
     * @var string
     */
    protected $uploadDir;
    
    /**
     *
     * @var \Zend\InputFilter\InputFilterInterface
     */
    protected $inputFilter;
    
    
    /**
     *
     * @var type \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;
    
    /**
    * @var string[]
    */
    protected $mimeTypes;
    
    /**
     *
     * @var string[] 
     */
    protected $extensions;
    
    /**
     * @var string 
     */
    protected $maxSize;
    
    
    /**
     * 
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter() {
        return $this->inputFilter;
    }

    /**
     * 
     * @return string
     */
    public function getUploadDir() {
        return $this->uploadDir;
    }

    /**
     * 
     * @param InputFilterInterface $inputFilter
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    /**
     * 
     * @param string $dir
     */
    public function setUploadDir($dir) {
        $this->uploadDir = $dir;
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
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * 
     * @return array of string
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * 
     * @return int
     */
    public function getMaxSize() {
        return $this->maxSize;
    }

    /**
     * 
     * @return array of string
     */
    public function getMimeTypes() {
        return $this->mimeTypes;
    }

    /**
     * 
     * @param array of string $extensions
     */
    public function setExtensions($extensions) {
        $this->extensions = $extensions;
    }

    /**
     * 
     * @param string $maxSize
     */
    public function setMaxSize($maxSize) {
        $this->maxSize = $maxSize;
    }

    /**
     * 
     * @param array of string $mimeTypes
     */
    public function setMimeTypes($mimeTypes) {
        $this->mimeTypes = $mimeTypes;
    }
    
    /**
     * 
     * @param array $fileData
     * @return string
     * @throws Exception\UploadedFileModelUploadFailedException
     */
    public function upload($fileData, $savedFileName, $overwrite = false){
        $savedFilePath = $this->uploadDir.'/'.$savedFileName;
        $input = $this->getInputFilter()->get('file');
        /*@var $input \Zend\InputFilter\InputInterface*/
        $input->getFilterChain()->attach(
                new \Zend\Filter\File\RenameUpload(
            [
                'target' => $savedFilePath,
                'overwrite' => $overwrite,
            ]
        )
        );
        if ($this->getInputFilter()->setData($fileData)->isValid()){
            return true;
        }
        else {
            throw new Exception\UploadedFileModelUploadFailedException(json_encode($this->getInputFilter()->getMessages()));
        }
    }
    
    /**
     * 
     * @param string $file
     * @param string $mimeType
     * @return boolean
     */
    public function isUploaded($file, $mimeType = null){
        $savedFilePath = $this->uploadDir.'/'.$file;
        $exists = is_file($savedFilePath) && filesize($savedFilePath) > 0;
        if (!$exists || !$mimeType){
            return $exists;
        }
        return mime_content_type($savedFilePath) == $mimeType;
    }

}
