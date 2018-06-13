<?php

namespace App\Components\FolderFile\Contracts;

use App\Components\Core\Result;

interface IFolderRepository
{
    /**
     * create new Folder
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload);

    /**
     * list all folders
     * @param $parent_id
     * @return Result
     */
    public function listFoldersByParentId($parent_id);

    /**
     * getparent
     * @param $parent_id
     * @return Result
     */
    public function getParent($id);

}