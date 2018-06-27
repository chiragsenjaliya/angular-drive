<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api']], function () {
	//User Controller
	Route::post('logout','Drive\\UserController@logout')->name('logout');	

	//FolderFile Controller
	Route::get('folder-tree/{slug?}','Drive\\FolderFileController@folderList')->name('folderList');
	Route::post('create-folder','Drive\\FolderFileController@createFolder')->name('createFolder');
	Route::get('get-folders-files/{slug?}','Drive\\FolderFileController@getFileFolder')->name('getFileFolder');
	Route::post('upload-file','Drive\\FolderFileController@uploadFile')->name('uploadFile');
	Route::get('file-download/{slug?}','Drive\\FolderFileController@getDownload')->name('getDownload');
	
});

Route::post('register','Drive\\UserController@registerUser')->name('register');
