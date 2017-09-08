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

/**
 *
 * @author ktogias
 */
interface UploadedFileModelInterface {
    /**
     * 
     * @param string $dir
     */
    public function setUploadDir($dir);
    
    /**
     * @return string
     */
    public function getUploadDir();
    
    /**
     * 
     * @param Zend\InputFilter\InputFilterInterface $inputFilter
     */
    public function setInputFilter(InputFilterInterface $inputFilter);
    
    /**
     * @return Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter();
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);
    
    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator();
    
    
    /**
     * 
     * @param array of string $mimeTypes
     */
    public function setMimeTypes($mimeTypes);
    
    /**
     * @return array of string 
     */
    public function getMimeTypes();
    
    /**
     * 
     * @param array of string $extensions
     */
    public function setExtensions($extensions);
    
    
    /**
     * @return array of string
     */
    public function getExtensions();
    
    
    /**
     * 
     * @param int $maxSize
     */
    public function setMaxSize($maxSize);
    
    
    /**
     * @return string
     */
    public function getMaxSize();
    
    /**
     * 
     * @param array $fileData
     */
    public function upload($fileData, $savedFileName);
    
    /**
     * 
     * @param string $file
     * @param string $mimetype
     * @return boolean
     */
    public function isUploaded($file, $mimeType = null);
    
}
