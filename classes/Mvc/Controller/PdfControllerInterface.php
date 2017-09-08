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
interface PdfControllerInterface extends AuthControllerInterface {
    
    /**
     * Add pdf files (given paths) to pdf object
     * 
     * @param array $files
     * @param boolean $force_odd
     */
    public function mergeFiles(array $files, $force_odd = FALSE);
    
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
    public function addHeaders(array $headers);
}
