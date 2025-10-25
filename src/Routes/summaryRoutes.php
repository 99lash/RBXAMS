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

/** 
 *  @method GET
 *  @route  /summary/csv
 *  @desc   Export daily activity summary as CSV
 */
$summaryRouter->get('/summary/csv', 'SummaryController@exportCsv');

/** 
 *  @method GET
 *  @route  /summary/pdf
 *  @desc   Export daily activity summary as PDF
 */
$summaryRouter->get('/summary/pdf', 'SummaryController@exportPdf');

/** 
 *  @method GET
 *  @route  /api/dashboard
 *  @desc   Get dashboard data for JS frontend
 */
$summaryRouter->get('/api/dashboard', 'SummaryController@getDashboardData');
