<?php

namespace srag\Plugins\SrUI\Config\Buttons;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ButtonColorConfig
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ButtonColorConfig extends BaseNestedConfig
{
    public const NS = 'button_colors';

    public const F_PRIMARY_BUTTON_COLOR = 'primary_button_color';
    public const F_PRIMARY_BUTTON_COLOR_HOVER = 'primary_button_color_hover';
    public const F_PRIMARY_BUTTON_COLOR_TEXT = 'primary_button_color_text';
    public const F_PRIMARY_BUTTON_COLOR_TEXT_HOVER = 'primary_button_color_text_hover';
    public const F_GENERAL_BUTTON_COLOR = 'general_button_color';
    public const F_GENERAL_BUTTON_COLOR_HOVER = 'general_button_color_hover';
    public const F_GENERAL_BUTTON_COLOR_TEXT = 'general_button_color_text';
    public const F_GENERAL_BUTTON_COLOR_TEXT_HOVER = 'general_button_color_text_hover';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getFields(): array
    {
        return [
            self::F_PRIMARY_BUTTON_COLOR,
            self::F_PRIMARY_BUTTON_COLOR_HOVER,
            self::F_PRIMARY_BUTTON_COLOR_TEXT,
            self::F_PRIMARY_BUTTON_COLOR_TEXT_HOVER,
            self::F_GENERAL_BUTTON_COLOR,
            self::F_GENERAL_BUTTON_COLOR_HOVER,
            self::F_GENERAL_BUTTON_COLOR_TEXT,
            self::F_GENERAL_BUTTON_COLOR_TEXT_HOVER,
        ];
    }

    public function getPrimaryColor(): ?string
    {
        return $this->data[self::F_PRIMARY_BUTTON_COLOR] ?? '';
    }

    public function getPrimaryTextColor(): ?string
    {
        return $this->data[self::F_PRIMARY_BUTTON_COLOR_TEXT] ?? '';
    }

    public function getPrimaryHoverColor(): ?string
    {
        return $this->data[self::F_PRIMARY_BUTTON_COLOR_HOVER] ?? '';
    }

    public function getPrimaryHoverTextColor(): ?string
    {
        return $this->data[self::F_PRIMARY_BUTTON_COLOR_TEXT_HOVER] ?? '';
    }

    public function getGeneralColor(): ?string
    {
        return $this->data[self::F_GENERAL_BUTTON_COLOR] ?? '';
    }

    public function getGeneralHoverColor(): ?string
    {
        return $this->data[self::F_GENERAL_BUTTON_COLOR_HOVER] ?? '';
    }

    public function getGeneralTextColor(): ?string
    {
        return $this->data[self::F_GENERAL_BUTTON_COLOR_TEXT] ?? '';
    }

    public function getGeneralHoverTextColor(): ?string
    {
        return $this->data[self::F_GENERAL_BUTTON_COLOR_TEXT_HOVER] ?? '';
    }
}
