<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;

/**
 * Interface ResourceStorage
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface ResourceStorage
{
    public function fromUpload(UploadResult $u) : string;

    public function fromPath(string $u, string $mime_type = null) : string;

    public function remove(string $identification) : bool;

    public function getDataURL(string $identification) : string;

    public function getRevisionInfo(string $identification) : array;
}
