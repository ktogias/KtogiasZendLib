<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Mvc\Controller;

/**
 *
 * @author ktogias
 */
interface XlsxControllerInterface extends AuthControllerInterface, FileControllerInterface{
    
    /**
     * 
     * @param string $docTitle
     */
    public function createObjPHPExcel($docTitle);
    
    /**
     * 
     * @param string $title
     * @return \PHPExcel_Worksheet
     */
    public function createSheet($title);
    
    /**
     * 
     * @param string $filenamePrefix
     * @return string
     * @throws \Exception
     */
    public function saveXlsx($filenamePrefix);
    
    /**
     * 
     * @param \PHPExcel_Worksheet $sheet
     * @param array $config
     */
    public function createHeading(\PHPExcel_Worksheet $sheet, array $config);
    
    /**
     * 
     * @param \PHPExcel_Worksheet $sheet
     * @param array or ResultSet $data
     */
    public function fillWithData(\PHPExcel_Worksheet $sheet, $data);
    
}
