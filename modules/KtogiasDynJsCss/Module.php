<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasDynJsCss;

use Zend\Mvc\MvcEvent;

class Module extends \KtogiasZendLib\Module\Module {
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    
    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->getSharedManager()
            ->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function(MvcEvent $e) {
                $controller = $e->getTarget();
                /*@var $controller \Zend\Mvc\Controller\AbstractActionController*/
                $controllerClass = new \ReflectionClass($controller);
                if( $controllerClass->implementsInterface('KtogiasZendLib\Mvc\Controller\HtmlControllerInterface')){
                    $this->setAllLinks($e, $controllerClass->getNamespaceName(), $controllerClass->getName());
                }
            }, 100);
    }
    
    protected function setAllLinks(MvcEvent $e, $namespace, $class){
        $this->setLinks($e, $namespace, $class, 'head-js-links');
        $this->setLinks($e, $namespace, $class, 'bottom-js-links');
        $this->setLinks($e, $namespace, $class, 'css-links');
    }

    protected function setLinks(MvcEvent $e, $namespace, $class, $name) {
        $links = $e->getViewModel()->getVariable($name);
        $excluded = [];
        if (!$links) {
            $links = [];
        }
        $config = $e->getApplication()->getServiceManager()->get('config');
        if (array_key_exists($name, $config)){
            $moduleLinks = [];
            $classLinks = [];
            $val = array_merge($links, array_reverse($config[$name]));
            if (array_key_exists($namespace, $val) && is_array($val[$namespace])){
                $moduleLinks = $val[$namespace];
                if (array_key_exists('exclude', $moduleLinks)){
                    if (is_array($moduleLinks['exclude'])){
                        $excluded = $moduleLinks['exclude'];
                    }
                    else {
                        $excluded[] = $moduleLinks['exclude'];
                    }
                    unset($moduleLinks['exclude']);
                }
            }
            if (array_key_exists($class, $val) && is_array($val[$class])){
                $classLinks = $val[$class];
                if (array_key_exists('exclude', $classLinks)){
                    if (is_array($classLinks['exclude'])){
                        $excluded = array_merge($excluded, $classLinks['exclude']);
                    }
                    else {
                        $excluded[] = $classLinks['exclude'];
                    }
                    unset($classLinks['exclude']);
                }
            }
            
            $toUnset = [];
            foreach ($val as $key => $link){
                if (is_array($link)){
                    $toUnset[] = $key;
                }
            }
            foreach($toUnset as $key){
                unset($val[$key]);
            }
            $val = array_merge($val, array_reverse($moduleLinks));
            $val = array_merge($val, array_reverse($classLinks));
            $val = array_unique($val);
            foreach ($excluded as $link){
                $key = array_search($link, $val);
                if ($key){
                    unset($val[$key]);
                }
            }
            $e->getViewModel()->setVariable($name, $val);
        }
    }
}