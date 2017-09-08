<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Module;

use KtogiasZendLib\Module\Exception;

/**
 * A class for modules. Extend it in your module.
 *
 * @author ktogias
 */
class Module {
    protected $dir = NULL;
    protected $namespace = NULL;
    
    public function getConfig()
    {
        if (!$this->dir){
            throw new Exception\ModuleDirNotSetException();
        }
        if (!$this->namespace){
            throw new Exception\ModuleNamespaceNotSetException();
        }
        return include $this->dir . '/config.php';
    }

    public function getAutoloaderConfig()
    {
        if (!$this->dir){
            throw new Exception\ModuleDirNotSetException();
        }
        if (!$this->namespace){
            throw new Exception\ModuleNamespaceNotSetException();
        }
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    $this->namespace => $this->dir . '/classes/' . $this->namespace,
                ],
            ],
        ];
    }
}
