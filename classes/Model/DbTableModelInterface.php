<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;


use KtogiasZendLib\Db\Table\DbTableInterface;

/**
 *
 * @author ktogias
 */
interface DbTableModelInterface extends ReadOnlyDbTableModelInterface{
    const SAVEMODE_INSERT_OR_UPDATE = 0;
    const SAVEMODE_FORCE_INSERT = 1;
    const SAVEMODE_FORCE_UPDATE = 2;
    
       
    /**
     * 
     * @param $data object or array
     * @return \KtogiasZendLib\Model\DbTableModelInterface
     */
    public function set($data);
    
    /**
     * @param $mode one of  \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_INSERT_OR_UPDATE, 
     *                      \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_FORCE_INSERT,
     *                      \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_FORCE_UPDATE
     *                      default \KtogiasZendLib\Model\DbTableModelInterface::SAVEMODE_INSERT_OR_UPDATE
     * @return \KtogiasZendLib\Model\DbTableModel
     */
    public function save($mode = DbTableModelInterface::SAVEMODE_INSERT_OR_UPDATE);
    
    /**
     * 
     */
    public function delete();
}
