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
 * Description of XlsxAuthController
 *
 * @author ktogias
 */
abstract class XlsxController extends AuthController implements XlsxControllerInterface {
    
    /**
     *
     * @var \PHPExcel
     */
    protected $objPHPExcel;
    
    /**
     * 
     * @param string $docTitle
     */
    public function createObjPHPExcel($docTitle) {
        $this->objPHPExcel = new \PHPExcel();
        $this->objPHPExcel->getProperties()
                ->setCreator($this->auth->getUser()->getFullname())
                ->setLastModifiedBy($this->auth->getUser()->getFullname())
                ->setTitle($docTitle);
        $this->objPHPExcel->removeSheetByIndex(0);
    }
    
    /**
     * 
     * @param string $title
     * @return \PHPExcel_Worksheet
     */
    public function createSheet($title){
        $sheet = $this->objPHPExcel->createSheet();
        $sheet->setTitle($title);
        return $sheet;
    }
    
    /**
     * 
     * @param string $filenamePrefix
     * @return string
     * @throws \Exception
     */
    public function saveXlsx($filenamePrefix){
       $file = $this->getFilePath($filenamePrefix).'.xlsx';
       $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
       $objWriter->save($file);
       return $file;
    }
    
    /**
     * 
     * @param \PHPExcel_Worksheet $sheet
     * @param array $config
     */
    public function createHeading(\PHPExcel_Worksheet $sheet, array $config){
        foreach ($config as $rowIndex => $row){
            $rowIndex++;
            foreach ($row['fields'] as $field){
                $sheet->mergeCellsByColumnAndRow($field['start_col'],$rowIndex,$field['start_col']+$field['range_cols']-1,$rowIndex)
                    ->getCellByColumnAndRow($field['start_col'],$rowIndex)
                    ->getStyle()->applyFromArray($row['style']);
                if (!empty($row['style']['wrapText'])){
                    $sheet->getCellByColumnAndRow($field['start_col'],$rowIndex)->getStyle()->getAlignment()->setWrapText(true);
                }
                if(!empty($field['autoSize'])){
                    $sheet->getColumnDimensionByColumn($field['start_col'])->setAutoSize(true);
                }
                $sheet->getCellByColumnAndRow($field['start_col'],$rowIndex)->setValue($field['value']);
            }
            if (!empty($row['style']['height'])){
                $sheet->getRowDimension($rowIndex)->setRowHeight($row['style']['height']);
            }
        }
        $sheet->freezePaneByColumnAndRow(0,$rowIndex+1);
    }
    
    /**
     * 
     * @param string $filenamePrefix
     * @return string
     * @throws \Exception
     */
    protected function getFilePath($filenamePrefix){
        $date = new \DateTime();
        $path = $this->getFilesDir();
        $file = $path.'/'.$filenamePrefix.'_'.$this->auth->getUser()->getId().'_'.$date->format('YmdHis');
        $i = 0;
        while (is_file($file)){
            $i++;
            $rand = rand(100000,999999);
            $file = $path.'/'.$filenamePrefix.'_'.$this->auth->getUser()->getId().'_'.$date->format('YmdHis').'_'.$rand;
            if ($i>100000){
                throw new \Exception('Max iterations to find available filename reached');
            }
        }
        return $file;
    }
}
