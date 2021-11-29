<?php

namespace srag\Plugins\SrUI\Config\Footer;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class AdditionalInfoConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class AdditionalInfoConfig extends BaseNestedConfig
{
    const NS = 'additional_footer_infos';
    const F_SHOW_RENDERED_BY = 'show_rendered_by';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function showRenderedBy() : bool
    {
        return $this->data[self::F_SHOW_RENDERED_BY] ?? false;
    }
}
