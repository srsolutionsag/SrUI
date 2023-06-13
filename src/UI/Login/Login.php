<?php

namespace srag\Plugins\SrUI\UI\Login;

use ILIAS\UI\Component\Breadcrumbs\Breadcrumbs;
use ILIAS\UI\Component\Image\Image;
use ILIAS\UI\Component\MainControls\Footer;
use ILIAS\UI\Component\MainControls\MainBar;
use ILIAS\UI\Component\MainControls\MetaBar;
use ILIAS\UI\Implementation\Component\Layout\Page\Standard;

/**
 * Class Login
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Login extends Standard
{
    /**
     * @var LoginPageSettings
     */
    protected $settings;

    public function __construct(
        LoginPageSettings $settings,
        array $content,
        MetaBar $metabar = null,
        MainBar $mainbar = null,
        Breadcrumbs $locator = null,
        Image $logo = null,
        Footer $footer = null,
        string $title = '',
        string $short_title = '',
        string $view_title = ''
    ) {
        $this->settings = $settings;
        parent::__construct($content, $metabar, $mainbar, $locator, $logo, $footer, $title, $short_title, $view_title);
    }

    /**
     * @return LoginPageSettings
     */
    public function getSettings() : LoginPageSettings
    {
        return $this->settings;
    }
}
