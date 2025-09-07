<?php

$router = new App\Config\Router;


/** 
 *  @method GET
 *  @route  /
 *  @desc   Render home page
 */
$router->get('/', 'HomeController@index');

/** 
 *  @method GET
 *  @route  /users
 *  @desc   Render users page
*/
$router->get('/users', 'UserController@index');