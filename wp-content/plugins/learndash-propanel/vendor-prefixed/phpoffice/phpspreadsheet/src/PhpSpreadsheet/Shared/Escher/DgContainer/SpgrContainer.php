<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer;

class SpgrContainer
{
    /**
     * Parent Shape Group Container.
     *
     * @var \LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer
     */
    private $parent;

    /**
     * Shape Container collection.
     *
     * @var array
     */
    private $children = [];

    /**
     * Set parent Shape Group Container.
     *
     * @param \LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Get the parent Shape Group Container if any.
     *
     * @return null|\LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add a child. This will be either spgrContainer or spContainer.
     *
     * @param mixed $child
     */
    public function addChild($child): void
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    /**
     * Get collection of Shape Containers.
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Recursively get all spContainers within this spgrContainer.
     *
     * @return SpgrContainer\SpContainer[]
     */
    public function getAllSpContainers()
    {
        $allSpContainers = [];

        foreach ($this->children as $child) {
            if ($child instanceof self) {
                $allSpContainers = array_merge($allSpContainers, $child->getAllSpContainers());
            } else {
                $allSpContainers[] = $child;
            }
        }

        return $allSpContainers;
    }
}
