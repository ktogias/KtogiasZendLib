<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

define('__KTOGIASZENDLIBDIR__', __DIR__);
spl_autoload_register(function($class){
        $classParts = explode('\\', $class);
        if ($classParts[0] == 'KtogiasZendLib'){
            array_shift($classParts);
            include __DIR__.'/classes/'.implode('/', $classParts).'.php';
        }
});
