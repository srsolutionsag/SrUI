<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Menu;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class MenuColorConfig
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MenuColorConfig extends BaseNestedConfig
{
    public const NS = 'menu_colors';

    public const F_MAIN_COLOR = 'main_color';
    public const F_MAIN_HOVER_COLOR = 'main_hover_color';
    public const F_SUB_COLOR = 'sub_color';
    public const F_SUB_HOVER_COLOR = 'sub_hover_color';
    public const F_TOOLS_COLOR = 'tools_color';
    public const F_TOOLS_HOVER_COLOR = 'tools_hover_color';
    public const F_ICON_COLOR = 'icon_color';
    public const V_ICON_COLOR_W_B = 'wb';
    public const V_ICON_COLOR_W_W = 'ww';
    public const V_ICON_COLOR_B_W = 'bw';
    public const V_ICON_COLOR_B_B = 'bb';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getFields(): array
    {
        return [
            self::F_MAIN_COLOR,
            self::F_MAIN_HOVER_COLOR,
            self::F_SUB_COLOR,
            self::F_SUB_HOVER_COLOR,
            self::F_TOOLS_COLOR,
            self::F_TOOLS_HOVER_COLOR,
            self::F_ICON_COLOR
        ];
    }

    public function getData()
    {
        if(!isset($this->data[self::F_ICON_COLOR])) {
            $this->data[self::F_ICON_COLOR] = self::V_ICON_COLOR_W_B;
        }

        return parent::getData();
    }

    public function getIconColor(): string
    {
        return $this->get(self::F_ICON_COLOR, self::V_ICON_COLOR_W_B);
    }

}
