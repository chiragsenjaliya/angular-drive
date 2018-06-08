<?php 

namespace App\Components\User\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
   
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','name','email','phone'
    ];

}