<?php

namespace App\Components\User\Contracts;

use App\Components\Core\Result;

interface ICompanyRepository
{
    /**
     * create new user
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload);

}