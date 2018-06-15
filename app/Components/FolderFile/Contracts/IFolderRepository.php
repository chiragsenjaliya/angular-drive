<?php

namespace App\Components\FolderFile\Contracts;

use App\Components\Core\Result;

interface IFolderRepository
{
    /**
     * get parent id
     * @param $slug
     * @return Result
     */
    public function getParentId($slug);

    /**
     * get folder id
     * @param $slug
     * @return $id
     */
    public function getFolderId($slug);

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
    public function listFoldersBySlug($parent_id);

    /**
     * getparent
     * @param $parent_id
     * @return Result
     */
    public function getParent($id);

}