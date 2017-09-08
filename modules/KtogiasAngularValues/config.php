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
            'ktogias-angular-values' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/ktogias-angular-values',
                    'defaults' => [
                        '__NAMESPACE__' => 'KtogiasAngularValues\Controller',
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
            'KtogiasAngularValues\Controller\Index' => 'KtogiasAngularValues\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ .'/angularjs',
        ],
    ],
    'head-js-links' => array_reverse([
        '/lib/angularjs/angular.js',
        '/lib/angularjs/angular-animate.js',
        '/lib/ui-bootstrap/ui-bootstrap-tpls.js',
        '/lib/ng-file-upload/ng-file-upload-shim.js',
        '/lib/ng-file-upload/ng-file-upload.js',
        '/lib/angular-validation-match/angular-validation-match.js',
        '/lib/ui-select/select.js',
        '/ktogias-angular-values'
    ]),
    
    'css-links' => array_reverse([
        '/lib/ui-select/select.css',
    ]),
];