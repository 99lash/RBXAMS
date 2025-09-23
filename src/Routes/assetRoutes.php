<?php

$assetRouter = &$router;

/** 
 *  @method GET
 *  @route  /accounts
 *  @desc   Get manage accounts page
 */
$assetRouter->get('/accounts', 'AssetController@index');

/** 
 *  @method GET
 *  @route  /accounts/new
 *  @desc   Get add account form page
 */
$assetRouter->get('/accounts/new', 'AssetController@create');
