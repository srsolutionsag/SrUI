<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\Refinery\Factory;
use ILIAS\Refinery\Transformation;
use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Trait ilSrUIHelperGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 */
trait ilSrUITrafos
{
    /**
     * @param Factory  $ref
     * @param Artifact $a
     * @return Closure
     */
    private function getArtifactSafer(Factory $ref, Artifact $a): Closure
    {
        return fn(string $key): Transformation => $this->getCustomTrafo($ref)(static fn($va) => $a[$key] = $va);
    }

    /**
     * @param Factory $ref
     * @return Closure
     */
    private function getCustomTrafo(Factory $ref): Closure
    {
        return static fn(callable $c): Transformation => $ref->custom()->transformation($c);
    }
}
