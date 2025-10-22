<?php
/* 
// 1. get account page (get)
// 2. get all accounts (get)
// 3. find account by id (get)
// 4. create account (post)
5. search account by name (frontend)
// 6. update account by id (patch)
// 7. solf delete account by id or name (delete)
8. filter by name | status | robux | cost php | cost rate | rate sold | price php | date added | unpend date | sold date (frontend)
// 9. bulk update accounts (patch)
// 10. bulk solt delete accounts (delete);
*/ 
$accountRouter = &$router;

/** 
 *  @method GET
 *  @route  /api/accounts
 *  @desc   Get all accounts as JSON
 */
$accountRouter->get('/api/accounts', 'AccountController@getAccountsJson');

/** 
 *  @method GET
 *  @route  /accounts
 *  @desc   Get manage accounts page
 */
$accountRouter->get('/accounts', 'AccountController@index');

/** 
 *  @method GET
 *  @route  /accounts/:id
 *  @desc   Get account by ID
 */
$accountRouter->get('/accounts/:id([0-9]+)', 'AccountController@getById');

/** 
 *  @method POST
 *  @route  /accounts
 *  @desc   Add a new asset
 */
$accountRouter->post('/accounts', 'AccountController@create');

/** 
 *  @method PATCH
 *  @route  /accounts/:id
 *  @desc   Update account by ID
 */
$accountRouter->patch('/accounts/:id([0-9]+)', 'AccountController@updateById');

/** 
 *  @method PATCH
 *  @route  /accounts/bulk-update/status
 *  @desc   Bulk update accounts by ID
 */
$accountRouter->patch('/accounts/bulk-update/status', 'accountController@updateStatusBulk');

/** 
 *  @method DELETE
 *  @route  /accounts/bulk-delete
 *  @desc   Bulk delete accounts by ID
 */
$accountRouter->delete('/accounts/bulk-delete', 'accountController@deleteBulk');