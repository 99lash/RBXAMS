<?php

$router = new App\Config\Router;


/** 
 *  @method GET
 *  @route  /
 *  @desc   Render home page
 */
$router->get('/', 'HomeController@index');

/** @group /accounts */

/** 
 *  @method GET
 *  @route  /accounts
 *  @desc   Get manage accounts page
*/
$router->get('/accounts', 'AssetController@index');

/** 
 *  @method GET
 *  @route  /accounts/new
 *  @desc   Get add account form page
*/
$router->get('/accounts/new', 'AssetController@create');

/** @group /summary */

/** 
 *  @method GET
 *  @route  /summary
 *  @desc   Get daily activity summary page 
*/
$router->get('/summary', 'SummaryController@index');


/** @group /auth */

/** 
 *  @method GET
 *  @route  /login
 *  @desc   Get login page 
*/
$router->get('/login', 'AuthController@login');

/** 
 *  @method GET
 *  @route  /register
 *  @desc   Get register page 
*/
$router->get('/register', 'AuthController@register');

/** 
 *  @method GET
 *  @route  /logout
 *  @desc   Request a logout action 
*/
$router->post('/logout', 'AuthController@logout');

/** @group /users */

/** 
 *  @method GET
 *  @route  /users
 *  @desc   Render users page
*/
$router->get('/users', 'UserController@index');
