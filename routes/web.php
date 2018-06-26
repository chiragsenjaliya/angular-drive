<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	$files = Zipper::make('storage/1/21-06-2018cccr.zip')->listFiles(); 	
	$zf = zip_open(public_path('storage/1/21-06-2018cccr.zip')); $i=1;
	while($zf && $ze = zip_read($zf)) {
	    $zi[$i]['zip entry name']= zip_entry_name($ze);
	    $zi[$i]['zip entry filesize']= zip_entry_filesize($ze);
	    $zi[$i]['zip entry compressed size']= zip_entry_compressedsize($ze);
	    $zi[$i]['zip entry compression method']= zip_entry_compressionmethod($ze);
	    $zi[$i]['zip entry open status'] = zip_entry_open($zf,$ze);
	    //$zi[$i]['zip entry file contents'] = zip_entry_read($ze,100);
	    $i++;
	}
	echo"<pre>";
	print_r($zi);
	zip_close($zf);
});
