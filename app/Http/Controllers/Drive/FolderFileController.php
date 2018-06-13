<?php

namespace App\Http\Controllers\Drive;

use App\Components\Core\Result;
use App\Components\FolderFile\Contracts\IFolderFileRepository;
use App\Components\FolderFile\Contracts\IFolderRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\FolderCollection;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class FolderFileController extends Controller
{
    /**
	 * @var IFolderRepository
	 */
	private $folderRepository;

	/**
	 * @var IFolderFileRepository
	 */
	private $folderfileRepository;

	/**
	 * FolderFileController constructor.
	 * @param IFolderRepository $folderRepository,
	 * @param IFolderFileRepository $folderfileRepository 
	 */
	public function __construct(IFolderRepository $folderRepository,IFolderFileRepository $folderfileRepository)
	{
	    $this->folderRepository = $folderRepository;
	    $this->folderfileRepository = $folderfileRepository;
	}

	/**
     * Get Folder List.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   	public function folderList($parent_id,Request $request)
   	{

   		$results = $this->folderRepository->listFoldersByParentId($parent_id);
   		
   		if(count($results->getData())>0)
   		{
   			$listfolders= $results->getData();   			

   			$folders=$this->buildFolderTree($listfolders); 

			return $this->sendResponse(
	            $results->getMessage(),
	            $folders
	        );
   		}
   		else
   		{
   			$folders =[];	
   			return $this->sendResponse(
                'folders',
                $folders,
                200
            );

   		}

   	}

   	/**
     * Arranging Folder data.
     *
     * @param  array  $folders
     * @return array  $folderstree
     */
   	protected function buildFolderTree($folders)
	{
	    $folderstree =[];

	    foreach ($folders as $key => $value) 
	    {
	    	
	    	$folderdata=[];
	    	$folderdata['id']=$value->id;
	    	$folderdata['name']=$value->name;
	    	

	    	$folderdata['path']=$this->getFolderPath($value->id,$value->parent_id,$value->name);

	    	if(count($value->children)>0)
	    	{
	    		$folderdata['hasChildren']=true;
	    	}

	    	$folderstree[]=$folderdata;

	    }	    
	    return $folderstree;
	}

	/**
     * Getting Folder path.
     *
     * @param  array  $folders
     * @return array  $folderstree
     */
	protected function getFolderPath($id,$parent_id,$path)
	{		
		if($parent_id == 0)
		{
			return $path;
		}
		else
		{
			$getParent=$this->folderRepository->getParent($parent_id)->getData();
			if(count($getParent->parent_id)==0)
	    	{	    		

	    		return $getParent->name.'/'.$path;

	    	}else{	    		
	    		return $this->getFolderPath($getParent->id,$getParent->parent_id,$getParent->name.'/'.$path);
	    	}
			
		}
	}

	/**
     * Create Folder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   	public function createFolder(Request $request)
   	{

   		$results = $this->folderRepository->create($request->all());
   		
   		if(count($results->getData())>0)
   		{
   			$folder= $results->getData();
   			$folderdata=[];
	    	$folderdata['id']=$folder->id;
	    	$folderdata['name']=$folder->name; 
	    	$folderdata['path']=$this->getFolderPath($folder->id,$folder->parent_id,$folder->name);

			return $this->sendResponse(
	            $results->getMessage(),
	            $folderdata
	        );
   		}
   		else
   		{
   			$folders =[];	
   			return $this->sendResponse(
                'folders',
                $folders,
                200
            );

   		}

   	}

   	public function getFileFolder($parent_id=0)
   	{   	
   		$results = $this->folderRepository->listFoldersByParentId($parent_id);
   		
   		if(count($results->getData())>0)
   		{
   			$listfolders= $results->getData();   			

   			$folders=$this->buildFolderTree($listfolders); 

			return $this->sendResponse(
	            $results->getMessage(),
	            $folders
	        );

   		}else{

   			return $this->sendResponse(
	            $results->getMessage(),
	            []
	        );
   		}
   	}
}
