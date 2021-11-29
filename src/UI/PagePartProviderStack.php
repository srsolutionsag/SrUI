<?php

namespace srag\Plugins\SrUI\UI;

use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\UI\Component\MainControls\MetaBar;
use ILIAS\UI\Component\Image\Image;
use ILIAS\UI\Component\Legacy\Legacy;
use ILIAS\UI\Component\MainControls\Footer;
use ILIAS\UI\Component\Breadcrumbs\Breadcrumbs;
use ILIAS\UI\Component\MainControls\MainBar;

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

    public function __construct(\ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider $parts)
    {
        $this->parts = $parts;
    }

    public function getContent() : ?Legacy
    {
        return $this->parts->getContent();
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

}
