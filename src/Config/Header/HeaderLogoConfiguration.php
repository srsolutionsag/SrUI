<?php

namespace srag\Plugins\SrUI\Config\Header;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class HeaderLogoConfiguration
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class HeaderLogoConfiguration extends BaseNestedConfig
{
    const NS = 'header_logo';
    const F_ICON = 'icon';
    const F_ICON_WIDTH = 'icon_width';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function hasHeaderLogo() : bool
    {
        return isset($this->data[self::F_ICON][0]);
    }

    public function getHeaderlogo() : string
    {
        return $this->data[self::F_ICON][0] ?? '';
    }

    public function getHeaderLogoWidth() : string
    {
        return $this->data[self::F_ICON_WIDTH] ?? '45px';
    }

    public function getFields() : array
    {
        return [
            self::F_ICON_WIDTH,
            self::F_ICON
        ];
    }
}
