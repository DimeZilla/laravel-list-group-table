<?php
/**
 * Some routes for our helping
 */

use DiamondLGTAble\Controllers\ExportDataController;

Route::group([
    'middleware'=> 'web'
], function () {
    Route::post('export-data', 'DiamondLGTAble\Controllers\ExportDataController@export')->name('lgtable-post-export-data');
    Route::get('export-data', 'DiamondLGTAble\Controllers\ExportDataController@export')->name('lgtable-get-export-data');
});

