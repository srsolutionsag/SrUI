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
 * Class HeaderLogoConfiguration
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class HeaderLogoConfiguration extends BaseNestedConfig
{
    public const NS = 'header_logo';
    public const F_ICON = 'icon';
    public const F_ICON_WIDTH = 'icon_width';
    public const F_ICON_LINK = 'icon_link';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getData()
    {
        $this->data[self::F_ICON_LINK] ??= '';
        return parent::getData();
    }

    public function hasHeaderLogo(): bool
    {
        return isset($this->data[self::F_ICON][0]);
    }

    public function getHeaderlogo(): string
    {
        return $this->data[self::F_ICON][0] ?? '';
    }

    public function getHeaderLogoWidth(): string
    {
        return $this->data[self::F_ICON_WIDTH] ?? '45px';
    }

    public function getHeaderIconLink(): string
    {
        return $this->data[self::F_ICON_LINK] ?? '';
    }

    public function getFields(): array
    {
        return [
            self::F_ICON_WIDTH,
            self::F_ICON,
            self::F_ICON_LINK,
        ];
    }
}
