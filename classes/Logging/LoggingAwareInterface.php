<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Logging;

/**
 *
 * @author ktogias
 */
interface LoggingAwareInterface {
    /**
     * @return \KtogiasZendLib\Application\Log\Model\LogModel
     */
    public function getLogModel();
    
    public function setLogModel(\KtogiasZendLib\Application\Log\Model\LogModelInterface $logModel);
}
