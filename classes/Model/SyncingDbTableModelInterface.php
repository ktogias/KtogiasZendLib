<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;

/**
 *
 * @author ktogias
 */
interface SyncingDbTableModelInterface extends DbTableModelInterface{
    
    /**
     * 
     * @param \KtogiasZendLib\Model\ReadOnlyDbTableModelInterface $parentModel
     * @param callable $callback
     */
    public function syncWithParent(\KtogiasZendLib\Model\ReadOnlyDbTableModelInterface $parentModel, callable $callback = NULL);
    
}
