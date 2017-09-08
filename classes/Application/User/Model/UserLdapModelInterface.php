<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Application\User\Model;

use KtogiasZendLib\Model\ValidatingDbTableModelInterface;
use KtogiasZendLib\Logging\LoggingAwareInterface;

/**
 *
 * @author ktogias
 */
interface UserLdapModelInterface extends ValidatingDbTableModelInterface, LoggingAwareInterface{
   
    /**
     * @return string
     */
    public function getCn();
    
    
    /**
     * @return string
     */
    public function getDn();
    
    /**
     * @return string
     */
    public function getEmployeeid();
    
    /**
     * @return string
     */
    public function getMail();
    
    /**
     * @return string
     */
    public function getProgram();
    
    /**
     * @return string
     */
    public function getUid();
    
    /**
     * @return integer
     */
    public function getUserId();
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt();
    
    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
    
    /**
     * @return \KtogiasZendLib\Application\User\Model\UserModelInterface
     */
    public function getUser();
    
    /**
     * 
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $user
     * @return $this
     * @throws Exception\WrongUserIdException
     */
    public function setUser(UserModelInterface $user);
    
    /**
     * @param $dn string
     * @return $this
     */
    public function loadByDn($dn);
    
    /**
     * 
     * @param array $data
     * @return $this
     * @throws Exception\LdapUidHasChangedException
     */
    public function updateFromData(array $data);
    
    /**
     * @param $uid string
     * @return $this
     */
    public function loadByUid($uid);
    
    /**
     * 
     * @param array $data
     * @return $this
     * @throws \Exception
     */
    public function createFromData(array $data);
    
    /**
     * 
     * @param array $data
     * @return $this
     */
    public function loadUpdateCreateFromLdapData(array $data);
    
}
