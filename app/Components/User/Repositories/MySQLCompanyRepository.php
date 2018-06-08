<?php

namespace App\Components\User\Repositories;


use App\Components\Core\Result;
use App\Components\User\Contracts\ICompanyRepository;
use App\Components\User\Models\Company;
use App\Components\Core\Utilities\Helpers;

class MySQLCompanyRepository implements ICompanyRepository
{

	/**
     * create new user
     *
     * 
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
    	// create the user
        $Company = Company::create([
            'user_id' => $payload['id'],
            'name' => $payload['company_name'],
            'email' => $payload['email'],            
            'phone' => $payload['phone']
        ]);

        if(!$Company) return new Result(false,'Company not found.',null, 404);      
        
        return new Result(true,'Company created.',$Company,201);
    }

}