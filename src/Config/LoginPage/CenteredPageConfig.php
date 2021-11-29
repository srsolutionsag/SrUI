<?php

namespace srag\Plugins\SrUI\Config\LoginPage;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class CenteredPageConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class CenteredPageConfig extends BaseNestedConfig
{
    const NS = 'centered_login_page';
    const F_SHOW_HEADER_LOGO = 'show_header_logo';
    const F_BACKGROUND_IMAGE = 'background_image';
    const F_CENTERED_LOGIN_PAGE_CSS = 'centered_login_page_css';

    public function __construct()
    {
        parent::__construct(self::NS);
    }
}
