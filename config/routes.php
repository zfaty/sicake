<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    $routes->connect('/departement_administratif', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'index'
        ]
    );

    $routes->connect('/departement_administratif/cree_commande', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'creeCommande'
        ]
    );

    $routes->connect('/departement_administratif/listing_commandes', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'listingCommandes'
        ]
    );
    
    $routes->connect('/departement_administratif/get_odin_orders/:ordernum', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'getOdinOrders'
        ],
        ['pass' => ['ordernum']]
    );
    

    $routes->connect('/departement_administratif/get_commandes_list', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'getCommandesList'
        ]
    );
    
    $routes->connect('/departement_administratif/get_list_pack', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'getListOfPack'
        ]
    );

    $routes->connect('/departement_administratif/get_plan_resources/:plan_id', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'getPlanResources'
        ],
        ['pass' => ['plan_id']]
    );    

    $routes->connect('/departement_administratif/save_cmd_comment/', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'saveComment'
        ]
    );
    $routes->connect('/departement_administratif/get_cmd_comments/:commande_id', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'getCmdComments'
        ],
        ['pass' => ['commande_id']]
    ); 

    $routes->connect('/departement_administratif/delete_cmd_comment/:id', 
        [
        'controller' => 'DepartementAdministratif', 
        'action' => 'deleteComment'
        ],
        ['pass' => ['id']]
    );
    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
