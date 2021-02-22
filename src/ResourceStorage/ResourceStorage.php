<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;

/**
 * Interface ResourceStorage
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface ResourceStorage
{
    public function store(UploadResult $u) : string;

    public function remove(string $identification) : bool;

    public function getDataURL(string $identification) : string;

    public function getRevisionInfo(string $identification) : array;
}
