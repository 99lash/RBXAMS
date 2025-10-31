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

/** 
 *  @method GET
 *  @route  /forgot-password
 *  @desc   Get forgot password page 
*/
$authRouter->get('/forgot-password', 'AuthController@forgotPasswordGet');

/** 
 *  @method POST
 *  @route  /forgot-password
 *  @desc   Handle forgot password email submission 
*/
$authRouter->post('/forgot-password', 'AuthController@forgotPasswordPost');

/** 
 *  @method GET
 *  @route  /reset-password
 *  @desc   Get reset password page with token 
*/
$authRouter->get('/reset-password', 'AuthController@resetPasswordGet');

/** 
 *  @method POST
 *  @route  /reset-password
 *  @desc   Handle reset password form submission 
*/
$authRouter->post('/reset-password', 'AuthController@resetPasswordPost');
