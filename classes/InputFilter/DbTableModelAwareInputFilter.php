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
use KtogiasZendLib\Model\DbTableModelInterface;


/**
 * Description of DbTableModelAwareInputFilter
 *
 * @author ktogias
 */
abstract class DbTableModelAwareInputFilter extends InputFilter implements DbTableModelAwareInputFilterInterface {
    /**
     *
     * @var  KtogiasZendLib\Model\DbTableModelInterface
     */
    protected $model;
    
    /**
     * 
     * @param DbTableModelInterface $model
     */
    public function __construct(DbTableModelInterface $model) {
        $this->model = $model;
    }
    
    /**
     * 
     * @return KtogiasZendLib\Model\DbTableModelInterface
     */
    public function getModel() {
        return $this->model;
    }

    
}
