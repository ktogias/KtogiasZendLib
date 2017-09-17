<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KtogiasZendLib\Application\User\Model;

use KtogiasZendLib\Model\ReadOnlyDbTableModelInterface;
use KtogiasZendLib\Permissions\Acl\Resource\ResourceInterface;
use KtogiasZendLib\Logging\LoggingAwareInterface;

/**
 *
 * @author ktogias
 */
interface UserModelInterface extends ReadOnlyDbTableModelInterface, LoggingAwareInterface{
    /**
     * @return array of \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getRoles();
    
    /**
     *
     * @param ResourceInterface $resource
     * @param type $privilege
     */
    public function isAllowed(ResourceInterface $resource, $privilege);
    
    /**
     * @return integer
     */
    public function getId();
    
    /**
     * @return array of \KtogiasZendLib\Application\Role\Model\RoleModel
     */
    public function getRolesWithAntecedents();
    
    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getAllByRole(\KtogiasZendLib\Application\Role\Model\RoleModel $role);
    
    /**
     * @return string
     */
    public function getUsername();
    
    /**
     * @return string
     */
    public function getFullname();
    
    /**
     * @return string
     */
    public function getFirstname();
    
    /**
     * @return string
     */
    public function getLastname();
    
    /**
     * 
     * @param string $roleAlias
     * @param boolean $withAntecedents default FALSE
     * @return boolean
     */
    public function hasRoleByAlias($roleAlias, $withAntecedents = false);
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModelInterface $role
     * @param boolean $withAntecedents default FALSE
     * @return boolean
     */
    public function hasRole(\KtogiasZendLib\Application\Role\Model\RoleModelInterface $role, $withAntecedents = false);
    
    /**
     * 
     * @return boolean
     */
    public function isActive();
    
    /**
     * 
     * @return boolean
     */
    public function hasPassword();
    
    /**
     * 
     * @return array
     */
    public function getSafeArrayCopy();
    
    /**
     * 
     * @param string $username
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param boolean $skipEmailValidation default false
     * @return $this
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidEmailException
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidUsernameException
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidFirstnameException
     * @throws \KtogiasZendLib\Application\User\Model\Exception\InvalidLastnameException
     */
    public function create($username, $email, $firstname, $lastname, $skipEmailValidation);
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModelInterface $role
     * @return $this
     * @throws \KtogiasZendLib\Application\User\Model\Exception\UserAlreadyHasThisRoleException
     */
    public function addRole(\KtogiasZendLib\Application\Role\Model\RoleModelInterface $role);
    /**
     * 
     * @return string
     */
    public function getEmail();
    
    /**
     * 
     * @param string $val
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $updater default NULL
     * @return $this
     * @throws Exception\InvalidFirstnameException
     */
    public function updateFirstname($val, UserModelInterface $updater = NULL);
    
    /**
     * 
     * @param string $val
     * @param \KtogiasZendLib\Application\User\Model\UserModelInterface $updater default NULL
     * @return $this
     * @throws Exception\InvalidLastnameException
     */
    public function updateLastname($val, UserModelInterface $updater = NULL);
    
     /**
     * 
     * @return $this
     */
    public function activate();
    
    /**
     * 
     * @return $this
     */
    public function deactivate();
    
    /**
     * @return boolean
     */
    public function isLdap();
    
    /**
     * @return \KtogiasZendLib\Application\User\Model\UserLdapModelInterface
     */
    public function getUserLdap();
    
    /**
     * 
     * @param \KtogiasZendLib\Application\Role\Model\RoleModelInterface $role
     * @return $this
     * @throws \KtogiasZendLib\Application\User\Model\Exception\UserDoesNotHaveThisRoleException
     */
    public function removeRole(\KtogiasZendLib\Application\Role\Model\RoleModelInterface $role);
    
    /**
     * 
     * @param string $mail
     * @return $this
     * @throws Exception\LdapUserNotFoundException
     */
    public function loadUpdateCreateFromLdapByMailOrUid($mailOrUid);
    
    /**
     * 
     * @return $this
     * @throws Exception\LdapUserNotFoundException
     */
    public function updateFromLdapByMail();
    
    /**
     * 
     * @param String $usernameOrEmail
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByUsernameOrEmail($usernameOrEmail);
    
    /**
     * 
     * @param String $email
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByEmail($email);
    
     /**
     * 
     * @param String $username
     * @return \KtogiasLogin\Model\UserModelInterface
     */
    public function loadByUsername($username);
    
    /**
     * 
     * @param string $password
     * @param string $salt
     * @param integer $iterations
     * @param integer $size
     * @return string
     */
    public function getKey($password, $salt, $iterations, $size);
    
    /**
     * 
     * @param string $data
     * @return string
     * @throws Exception\EmptyKeyException
     */
    public function encrypt($data);
    
    /**
     * 
     * @param string $data
     * @return string
     * @throws Exception\EmptyKeyException
     */
    public function decrypt($data);
    
}
