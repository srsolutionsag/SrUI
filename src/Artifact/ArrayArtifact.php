<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Artifact;

/**
 * An array as an artifact.
 */
class ArrayArtifact
{
    private array $data = [];

    /**
     * @param array $data - may only contain primitive data
     */
    public function __construct(array $data)
    {
        $this->check($data);
        $this->data = $data;
    }

    final public function serialize(): string
    {
        return "<?" . "php return " . var_export($this->data, true) . ";";
    }

    private function check(array $a)
    {
        foreach ($a as $item) {
            if (is_string($item) || is_int($item) || is_float($item) || is_bool($item) || is_null($item)) {
                continue;
            }
            if (is_array($item)) {
                $this->check($item);
                continue;
            }
            throw new \InvalidArgumentException(
                "Array data for artifact may only contain ints, strings, floats, bools or " .
                "other arrays with this content. Found: " . gettype($item)
            );
        }
    }
}
