<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI;

/**
 * Class Version
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Version
{
    public const GREATER_THAN = ">";
    public const SMALLER_THAN = "<";

    protected string $version;

    /**
     * @param string $version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function getVersionString(): string
    {
        return $this->version;
    }

    public function isNewerThan(Version $version): bool
    {
        return version_compare($this->version, $version->getVersionString(), self::GREATER_THAN);
    }

    public function isOlderThan(Version $version): bool
    {
        return version_compare($this->version, $version->getVersionString(), self::SMALLER_THAN);
    }
}
