<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\LoginPage;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ShowComponentsConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ShowComponentsConfig extends BaseNestedConfig
{
    public const NS = 'show_components';
    public const F_SHOW_HEADER = 'show_header';
    public const F_SHOW_FOOTER = 'show_footer';
    public const F_SHOW_MAIN_BAR = 'show_main_bar';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getFields(): array
    {
        return [
            self::F_SHOW_HEADER,
            self::F_SHOW_FOOTER,
            self::F_SHOW_MAIN_BAR,
        ];
    }
}
