<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;

/**
 * Interface ResourceStorage7
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ResourceStorage7 implements ResourceStorage
{
    public function store(UploadResult $u) : string
    {
        throw new \Exception('not yet implemented');
    }

    public function getDataURL(string $identification) : string
    {
        throw new \Exception('not yet implemented');
    }

    public function remove(string $identification) : bool
    {
        throw new \Exception('not yet implemented');
    }

    public function getRevisionInfo(string $identification) : array
    {
        throw new \Exception('not yet implemented');
    }

}
