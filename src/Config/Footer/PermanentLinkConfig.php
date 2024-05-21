<?php

namespace srag\Plugins\SrUI\Config\Footer;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PermanentLinkConfig extends BaseNestedConfig
{
    public const NS = 'permanent_link';
    public const F_SHOW_ENHACED_PERMANANT_LINK = 'show_enhanced_permanant_link';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function showEnhancedPermanantLink(): bool
    {
        return $this->data[self::F_SHOW_ENHACED_PERMANANT_LINK] ?? false;
    }

    public function getFields(): array
    {
        return [
            self::F_SHOW_ENHACED_PERMANANT_LINK
        ];
    }
}
