<?php

namespace App\Components\FolderFile\Contracts;

use App\Components\Core\Result;

interface IFolderFileRepository
{
    /**
     * create new File
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload);

}