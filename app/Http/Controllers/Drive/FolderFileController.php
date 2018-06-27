<?php

namespace App\Http\Controllers\Drive;

use App\Components\Core\Result;
use App\Components\FolderFile\Contracts\IFolderRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\FolderCollection;
use Auth;
use File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Response;

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

   	public function breadcrumbFolder($folderid,$array=[]){

   		$folder_array=[];

        if($folderid !=0 )
		{			
			$getParent=$this->folderRepository->getParent($folderid)->getData();
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
	    	$folderdata['path']=$this->getFolderPath($value->parent_id,$value->name);

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
	protected function getFolderPath($parent_id,$path)
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
	    		return $this->getFolderPath($getParent->parent_id,$getParent->name.'/'.$path);
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
	    	$folderdata['path']=$this->getFolderPath($folder->parent_id,$folder->name);
	    	$path = storage_path('app/public').'/'.Auth::guard('api')->user()->id.'/'.$folderdata['path'];
			File::makeDirectory($path, $mode = 0777, true, true);
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
   		$results = $this->folderRepository->listFolderFilesBySlug($slug);
   		$folderid=$this->folderRepository->getFolderId($slug);
   		$bradcrumb=$this->breadcrumbFolder($folderid);

   		if(count($results->getData())>0)
   		{
   			$listfolders= $results->getData();
   			$folderfiles=[];
   			foreach ($listfolders as $key => $value) {
   				
   				if($value->is_folder==0)
   				{
   					$path=asset('storage/'.Auth::guard('api')->user()->id.'/'.$this->getFolderPath($value->parent_id,'').$value->name);
   					$file_type=$this->getFileTypeHelper($value->mime_type);
   				}
   				else
   				{
   					$path="";
   					$file_type="folder";
   				}

   				$folderfiles[]=[
   					'name'=>$value->name,
   					'is_folder'=>$value->is_folder,
   					'id'=>$value->slug,
   					'file_type'=>$file_type,
   					'type'=>$value->mime_type,
   					'url'=>$path,
   					'meta_data'=>json_decode($value->meta_data,true)
   				];
   			}
			return $this->sendResponse(
	            $results->getMessage(),
	            ['folderfile'=>$folderfiles,'breadcrumb'=>$bradcrumb]
	        );

   		}else{

   			return $this->sendResponse(
	            $results->getMessage(),
	            ['folderfile'=>[],'breadcrumb'=>$bradcrumb]
	        );
   		}
   	}

   	public function uploadFile(Request $request){
   		$file=$request->file('fileItem');
   		$slug=$request->slug;
   		$folder=$this->folderRepository->getFolderBySlug($slug);

   		if(count($folder)==0)
   		{
   			$folder_id=0;
   		}
   		else
   		{
   			$folder_id=$folder->id;
   		}

   		$path=Auth::guard('api')->user()->id.'/'.$this->getFolderPath($folder_id,'');
   		$fileName =$file->getClientOriginalName();

   		if(!File::isDirectory(storage_path('app/public/'.$path.'/')))
   		{
   			File::makeDirectory(storage_path('app/public/'.$path.'/'), $mode = 0777, true, true);
   		}

   		if(File::exists(storage_path('app/public/'.$path.'/'.$fileName)))
   		{
   			$fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'('.substr(md5(microtime()),rand(0,26),6).').'.$file->getClientOriginalExtension();
   		}

   		$filesave = $file->storeAs($path,$fileName,'public');

   		if($filesave)
   		{
	   		$file_data=[];
	   		$file_data['name']=$fileName;
	   		$file_data['mime_type']=$file->getClientMimeType();
	   		$file_data['parent_id']=$folder_id;
	   		$file_data['meta_data']=['size'=>$file->getClientSize() ,'extention'=>$file->getClientOriginalExtension()];

	   		$results = $this->folderRepository->createFile($file_data);

	   		if(count($results->getData())>0)
	   		{
	   			return $this->sendResponse(
		            $results->getMessage(),
		            $results->getData()
		        );

	   		}else{
	   			return $this->sendResponse(
	                $results->getMessage(),
	                [],
	                403
	            );

	   		}
	   	}
	   	else
	   	{
	   		return $this->sendResponse(
	                "Error While Uploading! Try Again!",
	                [],
	                403
	            );
	   	}
   	}

   	public function getDownload($slug)
	{
	    $file=$this->folderRepository->getFolderBySlug($slug);
	    //PDF file is stored under project/public/download/info.pdf
	    $file_path=public_path('storage/'.Auth::guard('api')->user()->id.'/'.$this->getFolderPath($file->parent_id,'').$file->name);

	     $res_file=file_get_contents($file_path);
	    return  $res_file;
	}
}
