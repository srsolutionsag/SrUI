<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\MainMenu\Storage\Identification\ResourceIdentification;
use ILIAS\MainMenu\Storage\Services;

/**
 * Interface ResourceStorage
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ResourceStorage6 implements ResourceStorage
{
    /**
     * @var Services
     */
    protected $services;
    /**
     * @var Stakeholder
     */
    protected $stakeholder;

    /**
     * ResourceStorage6 constructor.
     */
    public function __construct()
    {
        $this->services    = new Services();
        $this->stakeholder = new Stakeholder();
    }

    public function store(UploadResult $u) : string
    {
        $i = $this->services->upload($u, $this->stakeholder);
        return $i->serialize();
    }

    public function getDataURL(string $identification) : string
    {
        $id = $this->services->find($identification);
        if ($id instanceof ResourceIdentification) {
            $revision = $this->services->getRevision($id);
            $stream   = $this->services->stream($id)->getStream();
            return 'data:' . $revision->getInformation()->getMimeType() . ';base64,' . base64_encode($stream->getContents());
        }
        return '';
    }

    public function remove(string $identification) : bool
    {
        $id = $this->services->find($identification);
        if ($id instanceof ResourceIdentification) {
            $this->services->remove($id);
            return true;
        }
        return false;
    }

    public function getRevisionInfo(string $identification) : array
    {
        $id = $this->services->find($identification);
        if ($id instanceof ResourceIdentification) {
            $info      = $this->services->getRevision($id)->getInformation();
            $title     = $info->getTitle();
            $size      = $info->getSize();
            $mime_type = $info->getMimeType();
        }
        return [
            'title'     => $title,
            'size'      => $size,
            'mime_type' => $mime_type
        ];
    }

}
