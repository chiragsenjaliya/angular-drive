<?php 

namespace App\Components\FolderFile\Models;

use Illuminate\Database\Eloquent\Model;

class FolderFile extends Model
{
   
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','name','folder_id','extention','mime_type','size'
    ];

}