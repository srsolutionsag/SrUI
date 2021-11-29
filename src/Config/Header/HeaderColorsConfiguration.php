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
    const F_TITLE_COLOR = 'header_title_color';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getBackgroundColor() : string
    {
        return $this->data[self::F_HEADER_BG_COLOR] ?? '';
    }

    public function getBreadcrumbBackgroundColor() : string
    {
        return $this->data[self::F_HEADER_BC_BG_COLOR] ?? '';
    }

    public function getBreadcrumbTextColor() : string
    {
        return $this->data[self::F_HEADER_BC_TEXT_COLOR] ?? '';
    }

    public function getTitleColor() : string
    {
        return $this->data[self::F_TITLE_COLOR] ?? '';
    }

    public function getData()
    {
        return [
            self::F_TITLE_COLOR => $this->getTitleColor(),
            self::F_HEADER_BG_COLOR => $this->getBackgroundColor(),
            self::F_HEADER_BC_BG_COLOR => $this->getBreadcrumbBackgroundColor(),
            self::F_HEADER_BC_TEXT_COLOR => $this->getBreadcrumbTextColor(),
        ];
    }

}
