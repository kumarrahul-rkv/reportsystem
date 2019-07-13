<?php
    Route::group(['namespace' => 'Ibarts\Reportsystem\Http\Controllers', 'middleware' => ['web'],'prefix' => 'member'], function(){
        Route::get('report',  'ReportController@index')->name('member.report')->middleware('auth');
        Route::get('report/create',  'ReportController@create')->name('member.report.create')->middleware('auth');
        Route::get('report/data',  'ReportController@data')->name('member.report.data')->middleware('auth');
        Route::get('report/show',  'ReportController@show')->name('member.report.show')->middleware('auth');
    });
?>