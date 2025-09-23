<?php

$summaryRouter = &$router;


/** 
 *  @method GET
 *  @route  /summary
 *  @desc   Get daily activity summary page 
 */
$summaryRouter->get('/summary', 'SummaryController@index');
