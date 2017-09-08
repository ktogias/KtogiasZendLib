<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasAngularValues\Controller;

use KtogiasZendLib\Mvc\Controller\Controller;
use KtogiasZendLib\Mvc\Controller\AngularJsControllerInterface;


/**
 * Description of IndexController
 *
 * @author ktogias
 */
class IndexController extends Controller implements AngularJsControllerInterface{
    
    public function indexAction(){
        $config = $this->serviceLocator->get('config');
        if (!empty($config['ktogias-login']['min-allowed-password-score'])){
            $minAllowedPasswordScore = $config['ktogias-login']['min-allowed-password-score'];
        }
        else {
            $minAllowedPasswordScore = 0;
        }
        return [
            'templates' => [
                'values/value.phtml' => 'value.phtml',
                'values/pending-edit.phtml' => 'pending-edit.phtml',
                'values/edit-buttons.phtml' => 'edit-buttons.phtml',
                'values/edit-text.phtml' => 'edit-text.phtml',
                'values/edit-password.phtml' => 'edit-password.phtml',
                'values/edit-long-text.phtml' => 'edit-long-text.phtml',
                'values/edit-date.phtml' => 'edit-date.phtml',
                'values/edit-file.phtml' => 'edit-file.phtml',
                'values/edit-map.phtml' => 'edit-map.phtml',
                'values/edit-ui-select-map.phtml' => 'edit-ui-select-map.phtml',
                'values/edit-ui-select-multiple-map.phtml' => 'edit-ui-select-multiple-map.phtml',
                'values/edit-email.phtml' => 'edit-email.phtml',
                'values/display-text.phtml' => 'display-text.phtml',
                'values/display-file.phtml' => 'display-file.phtml',
            ],
            'config' => [
                'min-allowed-password-score' => $minAllowedPasswordScore,
            ],
        ];
    }
    
    
}
