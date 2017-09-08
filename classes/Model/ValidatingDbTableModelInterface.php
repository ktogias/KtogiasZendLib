<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KtogiasZendLib\Model;

use Zend\InputFilter\InputFilterAwareInterface;
/**
 *
 * @author ktogias
 */
interface ValidatingDbTableModelInterface extends DbTableModelInterface, InputFilterAwareInterface{
    
    /**
     * 
     * @param $data object or array
     */
    public function validate($data);
}
