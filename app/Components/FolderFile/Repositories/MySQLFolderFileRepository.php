<?php

namespace App\Components\FolderFile\Repositories;


use App\Components\Core\Result;
use App\Components\FolderFile\Contracts\IFolderFileRepository;
use App\Components\FolderFile\Models\FolderFile;
use App\Components\Core\Utilities\Helpers;

class MySQLFolderFileRepository implements IFolderFileRepository
{

	/**
     * create new Folder File
     *
     * 
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
    	// create the Folder File
        $Company = FolderFile::create([
            'user_id' => $payload['id'],
            'name' => $payload['company_name'],
            'email' => $payload['email'],            
            'phone' => $payload['phone']
        ]);

        if(!$Company) return new Result(false,'Company not found.',null, 404);      
        
        return new Result(true,'Company created.',$Company,201);
    }

}