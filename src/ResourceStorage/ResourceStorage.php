<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;

/**
 * Interface ResourceStorage
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface ResourceStorage
{
    public function fromUpload(UploadResult $u): string;

    public function fromPath(string $u, string $mime_type = null): string;

    public function fromString(string $content, string $mime_type = null): string;

    public function remove(string $identification): bool;

    public function getDataURL(string $identification): string;

    public function getRevisionInfo(string $identification): array;

    public function has(string $identification): bool;

    public function getString(string $identification): string;

    public function getPath(string $identification): string;
}
