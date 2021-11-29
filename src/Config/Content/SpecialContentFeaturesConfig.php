<?php

namespace srag\Plugins\SrUI\Config\Content;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class SpecialContentFeaturesConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class SpecialContentFeaturesConfig extends BaseNestedConfig
{
    const NS = 'content';
    const F_FANCY_CARDS = 'fancy_cards';
    const F_FANCY_TABS = 'fancy_tabs';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function hasFancyCards() : bool
    {
        return $this->data[self::F_FANCY_CARDS] ?? false;
    }

    public function hasFancyTabs() : bool
    {
        return $this->data[self::F_FANCY_TABS] ?? false;
    }
}
