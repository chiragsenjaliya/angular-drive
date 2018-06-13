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
     * create new Folder
     *
     * 
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
    	// create the Folder
        $Folder = Folder::create([
            'user_id' => Auth::guard('api')->user()->id,
            'name' => $payload['name'],
            'parent_id' => $payload['parent_id'] 
        ]);

        if(!$Folder) return new Result(false,'Error While Folder Creating.',null, 403);      
        
        return new Result(true,'Folder created.',$Folder,200);
    }

    /**
     * list all folders
     *
     * @return Result
     */
    public function listFoldersByParentId($parent_id){

        $Folders=Folder::where('user_id',Auth::guard('api')->user()->id)->where('parent_id',$parent_id)->with(['children','parent'])->get();
        
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