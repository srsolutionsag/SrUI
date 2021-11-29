<?php

namespace srag\Plugins\SrUI\Config\Footer;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ColorConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ColorConfig extends BaseNestedConfig
{
    const F_FOOTER_BG_COLOR = 'footer_bg_color';
    const F_FOOTER_TEXT_COLOR = 'footer_text_color';
    const NS = 'footer_colors';

    public function __construct()
    {
        parent::__construct(self::NS);
    }
}
