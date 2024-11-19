<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Footer;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class AdditionalInfoConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class AdditionalInfoConfig extends BaseNestedConfig
{
    public const NS = 'additional_footer_infos';
    public const F_SHOW_RENDERED_BY = 'show_rendered_by';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function showRenderedBy(): bool
    {
        return $this->data[self::F_SHOW_RENDERED_BY] ?? false;
    }

    public function getFields(): array
    {
        return [
            self::F_SHOW_RENDERED_BY
        ];
    }
}
