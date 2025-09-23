<?php
$userRouter = &$router;

/** 
 *  @method GET
 *  @route  /users
 *  @desc   Get all users
 */
$userRouter->get('/users', 'UserController@listAll');

/** 
 *  @method GET
 *  @route  /users/:id
 *  @desc   Get user by ID
 */
$userRouter->get('/users/:id([A-Za-z0-9]+)', 'UserController@getById');

/** 
 *  @method POST
 *  @route  /users
 *  @desc   Create a new sub-user
 */
$userRouter->post('/users', 'UserController@create');

/** 
 *  @method PATCH
 *  @route  /users/:id
 *  @desc   Update user by ID
 */
$userRouter->patch('/users/:id([A-Za-z0-9]+)', 'UserController@updateById');

/** 
 *  @method DELETE
 *  @route  /users/:id
 *  @desc   Delete user by ID
 */
$userRouter->delete('/users/:id([A-Za-z0-9]+)', 'UserController@deleteById');