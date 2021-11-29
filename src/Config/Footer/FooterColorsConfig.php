<?php

namespace srag\Plugins\SrUI\Config\Footer;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class FooterColorsConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class FooterColorsConfig extends BaseNestedConfig
{
    const F_FOOTER_BG_COLOR = 'footer_bg_color';
    const F_FOOTER_TEXT_COLOR = 'footer_text_color';
    const NS = 'footer_colors';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getBackgroundColor() : string
    {
        return $this->data[self::F_FOOTER_BG_COLOR] ?? '';
    }

    public function getTextColor() : string
    {
        return $this->data[self::F_FOOTER_TEXT_COLOR] ?? '';
    }

    public function getData()
    {
        return [
            self::F_FOOTER_BG_COLOR => $this->getBackgroundColor(),
            self::F_FOOTER_TEXT_COLOR => $this->getTextColor(),
        ];
    }

}
