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
use KtogiasZendLib\Model\DbTableModelInterface;

/**
 *
 * @author ktogias
 */
interface DbTableModelAwareInputFilterInterface extends InputFilterInterface{
    
    /**
     * 
     * @param DbTableModelInterface $model
     */
    public function __construct(DbTableModelInterface $model);
    
    /**
     * @return KtogiasZendLib\Model\DbTableModelInterface
     */
    public function getModel();
    
}