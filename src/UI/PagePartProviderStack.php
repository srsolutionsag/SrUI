<?php

namespace srag\Plugins\SrUI\UI;

use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\UI\Component\MainControls\MetaBar;
use ILIAS\UI\Component\Image\Image;
use ILIAS\UI\Component\Legacy\Legacy;
use ILIAS\UI\Component\MainControls\Footer;
use ILIAS\UI\Component\Breadcrumbs\Breadcrumbs;
use ILIAS\UI\Component\MainControls\MainBar;
use ILIAS\UI\Component\MainControls\SystemInfo;

/**
 * Class PagePartProviderStack
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class PagePartProviderStack implements PagePartProvider
{
    /**
     * @var \ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider
     */
    protected $parts;
    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;
    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;

    protected $prepended_content = '';

    public function __construct(\ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider $parts)
    {
        global $DIC;
        $this->parts = $parts;
        $this->ui_factory = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
    }

    public function prependContent(string $content) : void
    {
        $this->prepended_content = $content . $this->prepended_content;
    }

    public function getContent() : ?Legacy
    {
        $legacy = $this->ui_renderer->render($this->parts->getContent());
        return $this->ui_factory->legacy($this->prepended_content . $legacy . "");
    }

    public function getMetaBar() : ?MetaBar
    {
        return $this->parts->getMetaBar();
    }

    public function getMainBar() : ?MainBar
    {
        return $this->parts->getMainBar();
    }

    public function getBreadCrumbs() : ?Breadcrumbs
    {
        return $this->parts->getBreadCrumbs();
    }

    public function getLogo() : ?Image
    {
        return $this->parts->getLogo();
    }

    public function getResponsiveLogo() : ?Image
    {
        return $this->parts->getResponsiveLogo();
    }

    public function getFooter() : ?Footer
    {
        return $this->parts->getFooter();
    }

    public function getTitle() : string
    {
        return $this->parts->getTitle();
    }

    public function getShortTitle() : string
    {
        return $this->parts->getShortTitle();
    }

    public function getViewTitle() : string
    {
        return $this->parts->getViewTitle();
    }

    public function getSystemInfos() : array
    {
        return $this->parts->getSystemInfos();
    }

}
