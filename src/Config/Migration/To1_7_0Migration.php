<?php

namespace srag\Plugins\SrUI\Config\Migration;

use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\Config\Menu\MenuColorConfig;

class To1_7_0Migration implements Migration
{
    public function appliesToVersionsPrior(): Version
    {
        return new Version('1.7.0');
    }

    public function perform(Configuration $configuration): void
    {
        $config = $configuration->get(MenuColorConfig::NS);
        $data = $config->getRawData();
        $data[MenuColorConfig::F_TOOLS_COLOR] = 'rgb(181, 79, 0)';
        $data[MenuColorConfig::F_TOOLS_HOVER_COLOR] = 'white';
        $config->loadData($data);
        $configuration->save();
    }
}
