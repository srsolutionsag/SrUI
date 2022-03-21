<?php

namespace srag\Plugins\SrUI\Config\Menu;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class MenuColorConfig
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MenuColorConfig extends BaseNestedConfig
{
    const NS = 'menu_colors';
    
    const F_MAIN_COLOR = 'main_color';
    const F_MAIN_HOVER_COLOR = 'main_hover_color';
    const F_SUB_COLOR = 'sub_color';
    const F_SUB_HOVER_COLOR = 'sub_hover_color';
    
    public function __construct()
    {
        parent::__construct(self::NS);
    }
    
    public function getFields() : array
    {
        return [
        
        ];
    }
    
}
