<?php

namespace srag\Plugins\SrUI\Config\LoginPage;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ShowComponentsConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ShowComponentsConfig extends BaseNestedConfig
{
    const NS = 'show_components';
    const F_SHOW_HEADER = 'show_header';
    const F_SHOW_FOOTER = 'show_footer';
    const F_SHOW_MAIN_BAR = 'show_main_bar';

    public function __construct()
    {
        parent::__construct(self::NS);
    }
}
