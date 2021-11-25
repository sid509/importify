<?php

use Illuminate\Support\Facades\Route;


Route::get('importify', 'Dws\Importify\ImportController@index');
Route::post('fetch-files', 'Dws\Importify\ImportController@fetchFile');
Route::post('process-files', 'Dws\Importify\ImportController@process');
Route::get('get-columns/{table?}', 'Dws\Importify\ImportController@getColumns')->name('getColumns');

