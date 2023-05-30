<?php

namespace srag\Plugins\SrUI\Config\Header;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class HeaderColorsConfiguration
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class HeaderColorsConfiguration extends BaseNestedConfig
{
    public const NS = 'header_colors';
    public const F_HEADER_BG_COLOR = 'header_bg_color';
    public const F_HEADER_BC_BG_COLOR = 'header_bc_bg_color';
    public const F_HEADER_BC_TEXT_COLOR = 'header_bc_text_color';
    public const F_TITLE_COLOR = 'header_title_color';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getBackgroundColor(): string
    {
        return $this->data[self::F_HEADER_BG_COLOR] ?? '';
    }

    public function getBreadcrumbBackgroundColor(): string
    {
        return $this->data[self::F_HEADER_BC_BG_COLOR] ?? '';
    }

    public function getBreadcrumbTextColor(): string
    {
        return $this->data[self::F_HEADER_BC_TEXT_COLOR] ?? '';
    }

    public function getTitleColor(): string
    {
        return $this->data[self::F_TITLE_COLOR] ?? '';
    }

    public function getFields(): array
    {
        return [
            self::F_TITLE_COLOR,
            self::F_HEADER_BG_COLOR,
            self::F_HEADER_BC_BG_COLOR,
            self::F_HEADER_BC_TEXT_COLOR,
        ];
    }
}
