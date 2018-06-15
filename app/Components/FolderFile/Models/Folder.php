<?php 

namespace App\Components\FolderFile\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends \Eloquent
{
   	protected $table = 'folders';
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','name','slug','parent_id','is_folder','mime_type','meta_data'
    ];    

    /**
     * User: belongsTo relationship
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Components\User\Models\User','user_id');
    }

    /**
    * Parent Comment: belongsTo relationship
    *
    * @return \App\Comment | null
    */
    public function parent()
    {
        return $this->belongsTo('App\Components\FolderFile\Models\Folder','parent_id');
    }

    /**
    * Children Comment: hasMany relationship
    *
    * @return \Illuminate\Database\Eloquent\Collection
    */        
    public function children()
    {
        return $this->hasMany('App\Components\FolderFile\Models\Folder','parent_id');
    }
    

}