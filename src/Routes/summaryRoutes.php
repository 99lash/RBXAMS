<?php

$summaryRouter = &$router;


/** 
 *  @method GET
 *  @route  /summary
 *  @desc   Get daily activity summary page 
 */
$summaryRouter->get('/summary', 'SummaryController@index');

/** 
 *  @method GET
 *  @route  /api/summary
 *  @desc   Get daily activity summary data for JS frontend
 */
$summaryRouter->get('/api/summary', 'SummaryController@getSummaryData');
