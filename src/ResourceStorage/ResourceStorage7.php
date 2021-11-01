<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\DI\Container;
use ILIAS\Filesystem\Stream\Streams;

/**
 * Interface ResourceStorage7
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ResourceStorage7 implements ResourceStorage
{

    /**
     * @var \ILIAS\ResourceStorage\Services
     */
    protected $services;
    /**
     * @var Stakeholder7
     */
    protected $stakeholder;

    public function __construct()
    {
        global $DIC;
        /**
         * @var $DIC Container
         */
        $this->services = $DIC->resourceStorage();
        $this->stakeholder = new Stakeholder7();
    }

    public function fromUpload(UploadResult $u) : string
    {
        return $this->services->manage()->upload(
            $u,
            $this->stakeholder
        );
    }

    public function fromPath(string $u, string $mime_type = null) : string
    {
        $stream = Streams::ofResource(fopen($u, "r"));
        return $this->services->manage()->stream(
            $stream,
            $this->stakeholder
        );
    }

    public function getDataURL(string $identification) : string
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return '';
        }
        return $this->services->consume()->src($identification)->getSrc();
    }

    public function remove(string $identification) : bool
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return false;
        }
        $this->services->manage()->remove($identification, $this->stakeholder);
        return true;
    }

    public function getRevisionInfo(string $identification) : array
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return [];
        }
        $info = $this->services->manage()->getCurrentRevision($identification)->getInformation();
        $title = $info->getTitle();
        $size = $info->getSize();
        $mime_type = $info->getMimeType();

        return [
            'title' => $title,
            'size' => $size,
            'mime_type' => $mime_type
        ];
    }

}
