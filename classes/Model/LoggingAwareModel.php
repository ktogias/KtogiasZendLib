<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */



namespace KtogiasZendLib\Model;

use KtogiasZendLib\Logging\LoggingAwareInterface;

use KtogiasZendLib\Application\Log\Model\LogModelInterface;

/**
 * Description of LoggingAwareModel
 *
 * @author ktogias
 */
abstract class LoggingAwareModel extends Model implements ModelInterface, LoggingAwareInterface{
    
    /**
     *
     * @var \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    protected $logModel;
    
    /**
     * 
     * @return \KtogiasZendLib\Application\Log\Model\LogModelInterface
     */
    public function getLogModel() {
        return $this->logModel;
    }

    /**
     * 
     * @param \KtogiasZendLib\Application\Log\Model\LogModelInterface $logModel
     * @return $this
     */
    public function setLogModel(LogModelInterface $logModel) {
        $this->logModel = $logModel;
        return $this;
    }

}
