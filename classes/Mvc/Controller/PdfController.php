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
 * Description of PdfController
 *
 * @author ktogias
 */
abstract class PdfController extends AuthController implements PdfControllerInterface {
    
    /**
     *
     * @var \FPDI 
     */
    protected $pdf;
    
    public function __construct() {
        parent::__construct();
        $this->pdf = new \FPDI();
    }
    
    /**
     * Add pdf files (given paths) to pdf object
     * 
     * @param array $files
     * @param boolean $force_odd
     * @param array $footers
     */
    public function mergeFiles(array $files, $force_odd = FALSE, array $footers = []){
        $pdf = $this->pdf;
        /*@var $pdf \FPDI*/
        $i = 1;
        $this->pdf->SetFont('freesans');
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFontSize(9);
        foreach ($files as $file){
            if ($force_odd && ($i-1)%2 == 1){
                $pdf->AddPage(null, 'A4');
            }
            $numPages = $pdf->setSourceFile($file['path']);
            for($i=1; $i<= $numPages; $i++){
                $pdf->AddPage();
                $pdf->useTemplate($pdf->importPage($i), null, null, 0, 0, true);
                foreach($footers as $footer){
                    if (empty($footer['align'])){
                        $footer['align'] = 'L';
                    }
                    if ($footer['type'] == 'mergefile_id'){
                        $content = 'Κωδ. αρχείου: '.$file['id'];
                    }
                }
                if (!empty($content)){
                    
                    $this->pdf->SetXY(9, -26);
                    $this->pdf->Write(0, $content, null, null, $footer['align']);
                }
            }
            
        }
       
    }
    
    /**
     * Add headers to every page
     * 
     * @param array $headers
     * @example [
     *       [
     *           'type' => 'global_page_num',
     *           'align' => 'R',
     *       ],
     *       [
     *           'type' => 'text',
     *           'text' => 'Αρ. πρ. 2017-345',
     *           'align' => 'L',
     *       ]
     *   ]
     */
    public function addHeaders(array $headers){
        $totalPages = $this->pdf->getNumPages();
        for ($i = 1; $i<=$totalPages; $i++){
            $this->pdf->setPage($i);
            $this->pdf->SetFont('freesans');
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFontSize(9);
            foreach ($headers as $header){
                if (empty($header['align'])){
                    $header['align'] = 'L';
                }
                if ($header['type'] == 'global_page_num'){
                    $content = 'Σελ. '.$this->pdf->getPage().'/'.$totalPages;
                }
                else if ($header['type'] == 'text'){
                    $content = $header['text'];
                }
                if (!empty($content)){
                    $this->pdf->SetXY(9, 3);
                    $this->pdf->Write(0, $content, null, null, $header['align']);
                }
            }
        }
    }
}
