<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasAngularAlerts\Controller;

use KtogiasZendLib\Mvc\Controller\Controller;
use KtogiasZendLib\Mvc\Controller\AngularJsControllerInterface;


/**
 * Description of IndexController
 *
 * @author ktogias
 */
class IndexController extends Controller implements AngularJsControllerInterface{
    
    protected $resourceId = 'KtogiasAngularAlerts\Controller\IndexController';
    
    public function indexAction(){
        return [
            'templates' => [
                'alerts' => 'alerts.phtml',
                'alert' => 'alert.phtml',
            ]
        ];
    }
    
    
}
