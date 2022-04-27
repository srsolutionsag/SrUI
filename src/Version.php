<?php

namespace srag\Plugins\SrUI;

/**
 * Class Version
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Version
{
    const ILIAS_SEVEN = '7.0';
    const GREATER_THAN = ">";
    const SMALLER_THAN = "<";
    protected $ilias_version = '';
    
    /**
     * @param string $ilias_version
     */
    public function __construct(string $ilias_version)
    {
        $this->ilias_version = $ilias_version;
    }
    
    public function sevenOrNewer() : bool
    {
        return version_compare($this->ilias_version, self::ILIAS_SEVEN, self::GREATER_THAN);
    }
    
    public function lessThanSeven() : bool
    {
        return version_compare($this->ilias_version, self::ILIAS_SEVEN, self::SMALLER_THAN);
    }
    
    public function hasCardFix() : bool
    {
        return version_compare($this->ilias_version, "7.7", self::GREATER_THAN)
            ||version_compare($this->ilias_version, "6.15", self::GREATER_THAN);
    }
    
}
