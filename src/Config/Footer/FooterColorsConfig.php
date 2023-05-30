<?php

namespace srag\Plugins\SrUI\Config\Footer;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class FooterColorsConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class FooterColorsConfig extends BaseNestedConfig
{
    public const F_FOOTER_BG_COLOR = 'footer_bg_color';
    public const F_FOOTER_TEXT_COLOR = 'footer_text_color';
    public const NS = 'footer_colors';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getBackgroundColor(): string
    {
        return $this->data[self::F_FOOTER_BG_COLOR] ?? '';
    }

    public function getTextColor(): string
    {
        return $this->data[self::F_FOOTER_TEXT_COLOR] ?? '';
    }

    public function getFields(): array
    {
        return [
            self::F_FOOTER_BG_COLOR,
            self::F_FOOTER_TEXT_COLOR
        ];
    }
}
