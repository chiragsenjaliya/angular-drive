<?php

namespace App\Components\FolderFile\Repositories;


use App\Components\Core\Result;
use App\Components\FolderFile\Contracts\IFolderRepository;
use App\Components\FolderFile\Models\Folder;
use App\Components\User\Models\User;
use App\Components\Core\Utilities\Helpers;
use Auth;

class MySQLFolderRepository implements IFolderRepository
{

    /**
     * get parent id
     * @param $slug
     * @return Result
     */
    public function getParentId($slug)
    {

        if($slug!='')
        {
            $Folder=Folder::where('user_id',Auth::guard('api')->user()->id)->where('slug',$slug)->first();
            $parent_id=$Folder->parent_id;
        }
        else
        {
            $parent_id=0;
        }
        
        return $parent_id;

    }

    /**
     * get folder id
     * @param $slug
     * @return $id
     */
    public function getFolderId($slug)
    {

        if($slug!='')
        {
            $Folder=Folder::where('user_id',Auth::guard('api')->user()->id)->where('slug',$slug)->first();
            $id=$Folder->id;
        }
        else
        {
            $id=0;
        }
        
        return $id;

    }

	/**
     * create new Folder
     *
     * 
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
    	$parent_id=$this->getFolderId($payload['slug']);

        // create the Folder
        $Folder = Folder::create([
            'user_id' => Auth::guard('api')->user()->id,
            'name' => $payload['name'],
            'parent_id' => $parent_id,
            'slug'=>(string) \Uuid::generate(),
            'is_folder'=>1
        ]);

        if(!$Folder) return new Result(false,'Error While Folder Creating.',null, 403);      
        
        return new Result(true,'Folder created.',$Folder,200);
    }

    /**
     * list all folders
     *
     * @return Result
     */
    public function listFoldersBySlug($slug)
    {
        $parent_id=$this->getFolderId($slug);
           
        $Folders=Folder::where('user_id',Auth::guard('api')->user()->id)->where('parent_id',$parent_id)->with(['children'=>function ($query)
            {
                $query->where('is_folder',1);
            },'parent'])->get();

        return new Result(true,'Children Folders',$Folders,200);        
    }

    /**
     * getparent
     * @param $id
     * @return Result
     */
    public function getParent($id){

        $Folder=Folder::where('user_id',Auth::guard('api')->user()->id)->where('id',$id)->with(['parent'])->first();
        
        return new Result(true,'Parent Folder',$Folder,200);

    }

}