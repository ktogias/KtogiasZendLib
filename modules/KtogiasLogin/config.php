<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

return ['router' =>
    ['routes' => 
        [
            'ktogias-login' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        '__NAMESPACE__' => 'KtogiasLogin\Controller',
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
                            'defaults' => [],
                        ],
                    ],
                    'password-reset' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/password-reset/token/:token',
                            'constraints' => [
                                'token' => '[a-zA-Z0-9_-]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'KtogiasLogin\Controller',
                                'controller'    => 'Index',
                                'action'        => 'passwordReset',
                            ],
                        ]
                    ],
                    'account-activation' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/account-activation/token/:token',
                            'constraints' => [
                                'token' => '[a-zA-Z0-9_-]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'KtogiasLogin\Controller',
                                'controller'    => 'Index',
                                'action'        => 'accountActivation',
                            ],
                        ]
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'KtogiasLogin\Controller\Index' => 'KtogiasLogin\Controller\IndexController',
            'KtogiasLogin\Controller\KtogiasLoginAngular' => 'KtogiasLogin\Controller\KtogiasLoginAngularController',
            'KtogiasLogin\Controller\KtogiasLoginJson' => 'KtogiasLogin\Controller\KtogiasLoginJsonController',
            'KtogiasLogin\Controller\KtogiasRetrievePasswordJson' => 'KtogiasLogin\Controller\KtogiasRetrievePasswordJsonController',
            'KtogiasLogin\Controller\KtogiasResetPasswordJson' => 'KtogiasLogin\Controller\KtogiasResetPasswordJsonController',
            'KtogiasLogin\Controller\KtogiasActivateAccountJson' => 'KtogiasLogin\Controller\KtogiasActivateAccountJsonController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ .'/views',
            __DIR__ .'/javascripts',
            __DIR__ .'/angularjs',
        ],
    ],
    'head-js-links' => array_reverse([
        '/lib/jquery/js/jquery.js',
        '/lib/jquery-ui/jquery-ui.js',
        '/lib/angularjs/angular.js',
        '/lib/angularjs/angular-route.js',
        '/lib/angularjs/angular-sanitize.js',
        '/lib/angularjs/i18n/angular-locale_el-gr.js',
        '/lib/angular-confirm/angular-confirm.js',
        'KtogiasLogin\Controller' => [
            '/lib/angular-validation-match/angular-validation-match.js',
            '/lib/zxcvbn/zxcvbn.js',
            '/login/ktogias-login-angular/ktogias-login-app',
            '/login/ktogias-login-angular/ktogias-login-module',
            '/login/ktogias-login-angular/ktogias-retrieve-password-module',
            '/login/ktogias-login-angular/ktogias-reset-password-module',
            '/login/ktogias-login-angular/ktogias-activate-account-module',
            'exclude' => [
                '/ktogias-angular-user-menu',
                '/ktogias-angular-auth'
            ]
        ],
    ]),
    'bottom-js-links' => array_reverse([
        '/lib/bootstrap/js/bootstrap.js',
        '/lib/bootstrap/js/ie10-viewport-bug-workaround.js',
    ]),
    'css-links' => array_reverse([
        '/lib/bootstrap/css/bootstrap.css',
        '/css/style.css'
    ]),
    'service_manager' => [
        'factories' => [
            'KtogiasLogin\Authentication\Adapter' => 'KtogiasLogin\Authentication\Factory\AdapterFactory',
            'KtogiasZendLib\Authnetication\UserModel' => 'KtogiasZendLib\Application\User\Model\Factory\UserModelFactory', 
            'KtogiasZendLib\Application\User\Model\UserModel' => 'KtogiasZendLib\Application\User\Model\Factory\UserModelFactory', 
            'KtogiasZendLib\Application\User\Db\Table\UserTable' => 'KtogiasZendLib\Application\User\Db\Table\Factory\UserTableFactory',
            'KtogiasZendLib\Application\Role\Model\RoleModel' => 'KtogiasZendLib\Application\Role\Model\Factory\RoleModelFactory', 
            'KtogiasZendLib\Application\Role\Db\Table\RoleTable' => 'KtogiasZendLib\Application\Role\Db\Table\Factory\RoleTableFactory',
            'KtogiasLogin\Model\UserModel' => 'KtogiasLogin\Model\Factory\UserModelFactory',
        ],
    ],
    
];