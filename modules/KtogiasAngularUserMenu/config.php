<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

return [
    'router' => [
        'routes' => [
            'ktogias-angular-user-menu' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/ktogias-angular-user-menu',
                    'defaults' => [
                        '__NAMESPACE__' => 'KtogiasAngularUserMenu\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                            'options' => [
                                'route'    => '/[:controller[/:action]]',
                                'constraints' => [
                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'KtogiasAngularUserMenu\Controller\Index' => 'KtogiasAngularUserMenu\Controller\IndexController',
            'KtogiasAngularUserMenu\Controller\Json' => 'KtogiasAngularUserMenu\Controller\JsonController',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'KtogiasAngularUserMenu\Model\UserMenuModel' => 'KtogiasAngularUserMenu\Model\Factory\UserMenuModelFactory', 
            'KtogiasAngularUserMenu\Db\Table\UserMenuTable' => 'KtogiasAngularUserMenu\Db\Table\Factory\UserMenuTableFactory'
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ .'/angularjs',
        ],
    ],
    'head-js-links' => array_reverse([
        '/lib/jquery/js/jquery.js',
        '/lib/angularjs/angular.js',
        '/lib/angularjs/angular-animate.js',
        '/lib/ui-bootstrap/ui-bootstrap-tpls.js',
        '/ktogias-angular-user-menu',
    ]),
];