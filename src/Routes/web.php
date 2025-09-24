<?php

App\Security\SessionManager::start();

$router = new \App\Config\Router;
/** 
 *  @method GET
 *  @route  /
 *  @desc   Render home page
 */
$router->get('/', 'HomeController@index');

/** 
 * @group /accounts 
 */

require_once __DIR__ . './assetRoutes.php';

/** 
 * @group /summary 
 * 
 */

require_once __DIR__ . './summaryRoutes.php';

/** 
 * @group /auth 
 */

require_once __DIR__ . './authRoutes.php';

/** 
 * @group /users 
 */
require_once __DIR__ . './userRoutes.php';

/** 
 *  @method GET
 *  @route  /guide
 *  @desc   Render guide page
 */
$router->get('/guide', 'GuideController@index');
