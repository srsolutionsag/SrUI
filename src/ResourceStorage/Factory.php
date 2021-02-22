<?php

namespace srag\Plugins\SrUI\ResourceStorage;

/**
 * Class Factory
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Factory
{
    public static function storage() : ResourceStorage
    {
        if (version_compare(ILIAS_VERSION_NUMERIC, '7.0', "<")) {
            return new ResourceStorage6();
        }
        return new ResourceStorage7();

    }
}
