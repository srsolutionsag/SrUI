<?php

namespace srag\Plugins\SrUI\Config\Header;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class HeaderColorsConfiguration
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class HeaderColorsConfiguration extends BaseNestedConfig
{
    const NS = 'header_colors';
    const F_HEADER_BG_COLOR = 'header_bg_color';
    const F_HEADER_BC_BG_COLOR = 'header_bc_bg_color';
    const F_HEADER_BC_TEXT_COLOR = 'header_bc_text_color';

    public function __construct()
    {
        parent::__construct(self::NS);
    }
}
