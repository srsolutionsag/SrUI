<?php declare(strict_types=1);

namespace srag\Plugins\SrUI\Config\Header;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ResponsiveHeaderLogoConfiguration extends BaseNestedConfig
{
    public const NS = 'responsive_header_logo';
    public const F_ICON = 'responsive_icon';
    public const F_ICON_WIDTH = 'responsive_icon_width';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function hasResponsiveHeaderLogo() : bool
    {
        return isset($this->data[self::F_ICON][0]);
    }

    public function getResponsiveHeaderlogo() : string
    {
        return $this->data[self::F_ICON][0] ?? '';
    }

    public function getResponsiveHeaderLogoWidth() : string
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
