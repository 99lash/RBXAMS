<?php

$authRouter = &$router;

/** 
 *  @method GET
 *  @route  /login
 *  @desc   Get login page 
*/
$authRouter->get('/login', 'AuthController@loginGet');

/** 
 *  @method POST
 *  @route  /login
 *  @desc   Login an account 
*/
$authRouter->post('/login', 'AuthController@loginPost');

/** 
 *  @method POST
 *  @route  /logout
 *  @desc   Request a logout action 
*/
$authRouter->post('/logout', 'AuthController@logoutPost');
