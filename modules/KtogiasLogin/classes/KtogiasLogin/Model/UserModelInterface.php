<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasLogin\Model;

use KtogiasZendLib\Application\User\Model\UserModelInterface as KtogiasZendLibUserModelInterface;

/**
 *
 * @author ktogias
 */
interface UserModelInterface extends KtogiasZendLibUserModelInterface{
    
    /**
     * 
     * @param String $username
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByUsername($username);
   
    /**
     * 
     * @param String $usernameOrEmail
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByUsernameOrEmail($usernameOrEmail);
    
    /**
     * 
     * @param string $token
     * @return \KtogiasLogin\Model\UserModel
     */
    public function loadByLoginToken($token);
    
    /**
     * 
     * @return string
     */
    public function getEmail();
    
    /**
     * 
     * @param string $token
     * @return \KtogiasLogin\Model\UserModel
     */
    public function setLoginToken($token);
    
    /**
     * 
     * @return string
     */
    public function getLoginToken();
    
    /**
     * 
     * @return \DateTime
     */
    public function getLoginTokenTimestamp();
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     */
    public function save();
    
    /**
     * 
     * @param string $token
     * @return boolean
     */
    public function loginTokenExists($token);
    
    /**
     * 
     * @param string $token
     * @return string
     * @throws Exception\EmptyModelException
     * @throws Exception\EmptySaltException
     */
    public function encryptToken($token);
    
    /**
     * Returns the hashed form of the provided password, sutiable to store to db.
     * If the user is LDAP no hashing is performed and the original password is returned.
     * 
     * @param string $password
     * @return string
     * @throws Exception\EmptyModelException
     * @throws Exception\EmptyPasswordException
     * @throws Exception\WeakPasswordException
     * @throws Exception\EmptySaltException
     * @throws Exception\HashMethodNotSupportedException
     */
    public function hashPassword($password);
    
    /**
     * 
     * @param string $password
     * @return \KtogiasLogin\Model\UserModel
     */
    public function setPassword($password);
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     */
    public function removeLoginToken();
    
    /**
     * 
     * @param string $password
     * @return boolean
     */
    public function verifyPassword($password);
    
    /**
     * 
     * @return array
     * @throws Exception\TokenNotSecureException
     * @throws Exception\TokenGenerationMaxTriesReached
     */
    public function generateToken();
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     * @throws Exception\EmptyPasswordException
     */
    public function activate();
    
    /**
     * 
     * @return boolean
     */
    public function isLoginTokenValid();
    
    /**
     * 
     * @return \KtogiasLogin\Model\UserModel
     */
    public function deactivate();
    
     /**
     * 
     * @param string $password
     * @return boolean
     */
    public function verifyLdapPassword($password);
}
