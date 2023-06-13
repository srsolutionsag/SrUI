<?php

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
    /**
     * @return bool
     */
    public function isCenterLogin() : bool
    {
        return $this->center_login;
    }

    /**
     * @param bool $center_login
     */
    public function setCenterLogin(bool $center_login) : void
    {
        $this->center_login = $center_login;
    }

    /**
     * @return bool
     */
    public function isShowHeader() : bool
    {
        return $this->show_header;
    }

    /**
     * @param bool $show_header
     */
    public function setShowHeader(bool $show_header) : void
    {
        $this->show_header = $show_header;
    }

    /**
     * @return bool
     */
    public function isShowFooter() : bool
    {
        return $this->show_footer;
    }

    /**
     * @param bool $show_footer
     */
    public function setShowFooter(bool $show_footer) : void
    {
        $this->show_footer = $show_footer;
    }

    /**
     * @return string
     */
    public function getBackgroundImage() : string
    {
        return $this->background_image;
    }

    /**
     * @param string $background_image
     */
    public function setBackgroundImage(string $background_image) : void
    {
        $this->background_image = $background_image;
    }

    /**
     * @return bool
     */
    public function isShowMainBar() : bool
    {
        return $this->show_main_bar;
    }

    /**
     * @param bool $show_main_bar
     */
    public function setShowMainBar(bool $show_main_bar) : void
    {
        $this->show_main_bar = $show_main_bar;
    }

    /**
     * @return bool
     */
    public function isShowHeaderLogo() : bool
    {
        return $this->show_header_logo;
    }

    /**
     * @param bool $show_header_logo
     */
    public function setShowHeaderLogo(bool $show_header_logo) : void
    {
        $this->show_header_logo = $show_header_logo;
    }
}
