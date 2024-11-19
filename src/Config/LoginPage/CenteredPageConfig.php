<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\LoginPage;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class CenteredPageConfig
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class CenteredPageConfig extends BaseNestedConfig
{
    public const NS = 'centered_login_page';
    public const F_SHOW_HEADER_LOGO = 'show_header_logo';
    public const F_BACKGROUND_IMAGE = 'background_image';
    public const F_CENTERED_LOGIN_PAGE_CSS = 'centered_login_page_css';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function showHeaderLogoInLoginContainer(): bool
    {
        return $this->data[self::F_SHOW_HEADER_LOGO] ?? false;
    }

    public function getData()
    {
        if ($this->data === null) {
            return null;
        }

        if (
            $this->data[self::F_CENTERED_LOGIN_PAGE_CSS] === null
            && $this->data[self::F_SHOW_HEADER_LOGO] === null
            && $this->data[self::F_BACKGROUND_IMAGE] === null
        ) {
            return null;
        }

        return $this->data ?? [
            self::F_SHOW_HEADER_LOGO => false,
            self::F_BACKGROUND_IMAGE => null,
            self::F_CENTERED_LOGIN_PAGE_CSS => ''
        ];
    }

    public function getFields(): array
    {
        return [
            self::F_SHOW_HEADER_LOGO,
            self::F_BACKGROUND_IMAGE,
            self::F_CENTERED_LOGIN_PAGE_CSS,
        ];
    }

    public function getLoginCSS(): ?string
    {
        return $this->data[self::F_CENTERED_LOGIN_PAGE_CSS] ?? '';
    }
}
