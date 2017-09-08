<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\InputFilter;

use Zend\InputFilter\InputFilterInterface;
use KtogiasZendLib\Model\UploadedFileModelInterface;

/**
 *
 * @author ktogias
 */
interface UploadedFileModelAwareInputFilterInterface extends InputFilterInterface{
    
    /**
     * 
     * @param UploadedFileModelInterface $model
     */
    public function __construct(UploadedFileModelInterface $model);
    
    /**
     * @return KtogiasZendLib\Model\UploadedFileModelInterface
     */
    public function getModel();
    
}