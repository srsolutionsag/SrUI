<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Header;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class HeaderTitleConfiguration
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class HeaderTitleConfiguration extends BaseNestedConfig
{
    public const NS = 'header_title';
    public const F_HEADER_TITLE = 'header_title';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getHeaderTitle(): string
    {
        return $this->data[self::F_HEADER_TITLE] ?? '';
    }

    public function getFields(): array
    {
        return [
            self::F_HEADER_TITLE
        ];
    }
}
