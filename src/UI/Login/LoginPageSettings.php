<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\UI\Login;

/**
 * Class LoginPageSettings
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class LoginPageSettings
{
    protected bool $center_login = true;
    protected bool $show_header = false;
    protected bool $show_footer = false;
    protected bool $show_main_bar = false;
    protected bool $show_header_logo = false;
    protected string $background_image = '';
    protected string $additional_html = '';

    public function isCenterLogin(): bool
    {
        return $this->center_login;
    }

    public function setCenterLogin(bool $center_login): void
    {
        $this->center_login = $center_login;
    }

    public function isShowHeader(): bool
    {
        return $this->show_header;
    }

    public function setShowHeader(bool $show_header): void
    {
        $this->show_header = $show_header;
    }

    public function isShowFooter(): bool
    {
        return $this->show_footer;
    }

    public function setShowFooter(bool $show_footer): void
    {
        $this->show_footer = $show_footer;
    }

    public function getBackgroundImage(): string
    {
        return $this->background_image;
    }

    public function setBackgroundImage(string $background_image): void
    {
        $this->background_image = $background_image;
    }

    public function isShowMainBar(): bool
    {
        return $this->show_main_bar;
    }

    public function setShowMainBar(bool $show_main_bar): void
    {
        $this->show_main_bar = $show_main_bar;
    }

    public function isShowHeaderLogo(): bool
    {
        return $this->show_header_logo;
    }

    public function setShowHeaderLogo(bool $show_header_logo): void
    {
        $this->show_header_logo = $show_header_logo;
    }

    public function getAdditionalHtml(): string
    {
        return $this->additional_html;
    }

    public function setAdditionalHtml(string $additional_html): void
    {
        $this->additional_html = $additional_html;
    }

}
