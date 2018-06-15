<?php

namespace App\Http\Controllers\Drive;

use App\Components\Core\Result;
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
	 * FolderFileController constructor.
	 * @param IFolderRepository $folderRepository,
	 * @param IFolderFileRepository $folderfileRepository 
	 */
	public function __construct(IFolderRepository $folderRepository)
	{
	    $this->folderRepository = $folderRepository;
	}

	/**
     * Get Folder List.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   	public function folderList($slug="",Request $request)
   	{
   		$root="";
   		$results = $this->folderRepository->listFoldersBySlug($root);   
   		$slugparent=$this->folderRepository->getParentId($slug);   		
		$slugparent_arr=$this->getFolderparentids($slugparent,[]);		 		
   		if(count($results->getData())>0)
   		{
   			$listfolders= $results->getData();   			

   			$folders=$this->buildFolderTree($listfolders,$slugparent_arr,$slug); 

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

   	public function breadcrumbFolder($parent_id,$array=[]){

   		$folder_array=[];

        if($parent_id !=0 )
		{			
			$getParent=$this->folderRepository->getParent($parent_id)->getData();
    		$folder_array['id']=$getParent->slug;
    		$folder_array['name']=$getParent->name;
    		$array[]=$folder_array;	    		
    		return $this->breadcrumbFolder($getParent->parent_id,$array);
		}
		return array_reverse($array);
    }

   	/**
     * Arranging Folder data.
     *
     * @param  array  $folders
     * @return array  $folderstree
     */
   	protected function buildFolderTree($folders,$slugparent_arr=[],$slug_main="")
	{
	    $folderstree =[];

	    foreach ($folders as $key => $value) 
	    {
	    	
	    	$folderdata=[];
	    	$folderdata['id']=$value->slug;
	    	$folderdata['name']=$value->name;
	    	
	    	if(in_array($value->id,$slugparent_arr)){
	    		$folderdata['isExpanded']=true;	
	    	}

	    	if($slug_main==$value->slug){
	    		$folderdata['isActive']=true;
	    		$folderdata['isFocused']=true;	    		
	    	}
	    	$folderdata['path']=$this->getFolderPath($value->id,$value->parent_id,$value->name);

	    	if(count($value->children)>0)
	    	{
	    		$folderdata['hasChildren']=true;
	    		$result_s = $this->folderRepository->listFoldersBySlug($value->slug);
    			if(count($result_s->getData())>0)
		   		{
		   			$listfolders= $result_s->getData();   			

		   			$folderdata['children']=$this->buildFolderTree($listfolders,$slugparent_arr,$slug_main);
		   		}
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
     * Getting Folder parentids.
     *
     * @param  int  $parent_id
     * @return array  $path
     */
	protected function getFolderparentids($parent_id,$path)
	{		
		if($parent_id == 0)
		{
			return $path;
		}
		else
		{
			$getParent=$this->folderRepository->getParent($parent_id)->getData();
					
    		$path[]=$getParent->id;
    		
    		return $this->getFolderparentids($getParent->parent_id,$path);	    	
			
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
	    	$folderdata['id']=$folder->slug;
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

   	public function getFileFolder($slug="")
   	{   	
   		$results = $this->folderRepository->listFoldersBySlug($slug);
   		$parent_id=$this->folderRepository->getParentId($slug);
   		$bradcrumb=$this->breadcrumbFolder($parent_id);

   		if(count($results->getData())>0)
   		{
   			$listfolders= $results->getData();   			

   			$folders=$this->buildFolderTree($listfolders); 

			return $this->sendResponse(
	            $results->getMessage(),
	            ['folderfile'=>$folders,'breadcrumb'=>$bradcrumb]
	        );

   		}else{

   			return $this->sendResponse(
	            $results->getMessage(),
	            ['folderfile'=>[],'breadcrumb'=>$bradcrumb]
	        );
   		}
   	}
}
