<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Content;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class SpecialContentFeaturesConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class SpecialContentFeaturesConfig extends BaseNestedConfig
{
    public const NS = 'content';
    public const F_FANCY_CARDS = 'fancy_cards';
    public const F_FANCY_TABS = 'fancy_tabs';
    public const F_VIEW_PORT = 'view_port';
    public const F_TAB_LINK_COLOR = 'tab_link_color';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function hasFancyCards(): bool
    {
        return $this->data[self::F_FANCY_CARDS] ?? false;
    }

    public function hasFancyTabs(): bool
    {
        return $this->data[self::F_FANCY_TABS] ?? false;
    }

    public function hasViewPortSetting(): bool
    {
        return $this->data[self::F_VIEW_PORT] ?? false;
    }

    public function getTabLinkColor(): ?string
    {
        return $this->data[self::F_TAB_LINK_COLOR] ?? null;
    }

    public function getFields(): array
    {
        return [
            self::F_FANCY_TABS,
            self::F_FANCY_CARDS,
            self::F_VIEW_PORT,
            self::F_TAB_LINK_COLOR
        ];
    }
}
