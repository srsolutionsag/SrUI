<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\UI\Login;

use ILIAS\UI\Component\Breadcrumbs\Breadcrumbs;
use ILIAS\UI\Component\Image\Image;
use ILIAS\UI\Component\MainControls\Footer;
use ILIAS\UI\Component\MainControls\MainBar;
use ILIAS\UI\Component\MainControls\MetaBar;
use ILIAS\UI\Implementation\Component\Layout\Page\Standard;
use ILIAS\UI\Component\Toast\Container;

/**
 * Class Login
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Login extends Standard
{
    protected LoginPageSettings $settings;

    public function __construct(
        LoginPageSettings $settings,
        array $content,
        ?MetaBar $metabar = null,
        ?MainBar $mainbar = null,
        ?Breadcrumbs $locator = null,
        ?Image $logo = null,
        ?Image $responsive_logo = null,
        string $favicon_path = '',
        ?Container $overlay = null,
        ?Footer $footer = null,
        string $title = '',
        string $short_title = '',
        string $view_title = ''
    ) {
        $this->settings = $settings;
        parent::__construct(
            $content,
            $metabar,
            $mainbar,
            $locator,
            $logo,
            $responsive_logo,
            $favicon_path,
            $overlay,
            $footer,
            $title,
            $short_title,
            $view_title
        );
    }

    /**
     * @return LoginPageSettings
     */
    public function getSettings(): LoginPageSettings
    {
        return $this->settings;
    }

    public function hasMainbar(): bool
    {
        return $this->settings->isShowMainBar() && $this->getMainbar() !== null;
    }
}
