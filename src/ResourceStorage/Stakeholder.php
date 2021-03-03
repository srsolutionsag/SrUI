<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\MainMenu\Storage\Resource\Stakeholder\AbstractResourceStakeholder;

/**
 * Class Stakeholder
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Stakeholder extends AbstractResourceStakeholder
{
    public function getId() : string
    {
        return "srui";
    }

}
