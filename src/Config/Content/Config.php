<?php

namespace srag\Plugins\SrUI\Config\Content;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class Config
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Config extends BaseNestedConfig
{
    const F_CONTENT = 'content';
    const F_FANCY_CARDS = 'fancy_cards';
    const F_FANCY_TABS = 'fancy_tabs';

    public function __construct()
    {
        parent::__construct(self::F_CONTENT);
    }
}
