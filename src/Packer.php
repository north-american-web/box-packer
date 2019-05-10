<?php


namespace NAWebCo\BoxPacker;


class Packer
{
    /**
     * @var Container[]
     */
    protected $containers = [];

    /**
     * @var SolidInterface[]
     */
    protected $items = [];

    /**
     * Packer constructor.
     * @param Container|Container[] $containers
     * @param SolidInterface[] $items
     */
    public function __construct($containers = [], $items = [])
    {
        $this->setContainers($containers);
        $this->setItems($items);
    }

    /**
     * @param Container|Container[] $containers
     * @return $this
     */
    public function setContainers($containers)
    {
        if (is_array($containers)) {
            foreach ($containers as $container) {
                $this->addContainer($container);
            }
        } else {
            $this->addContainer($containers);
        }

        return $this;
    }

    /**
     * @param Container $container
     * @return $this
     */
    public function addContainer(Container $container)
    {
        $this->containers[] = $container;
        return $this;
    }

    /**
     * @param $items
     * @return $this
     */
    public function setItems($items)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
        } else {
            $this->addItem($items);
        }
        return $this;
    }

    /**
     * @param SolidInterface $solid
     * @return $this
     */
    public function addItem(SolidInterface $solid)
    {
        $this->items[] = $solid;
        return $this;
    }

    /**
     * @return PackingResult
     * @throws InvalidPackingScenarioError
     */
    public function pack()
    {
        if (!$this->items) {
            throw new InvalidPackingScenarioError('There are no items to pack. Use setItems or addItem to add items.');
        }

        if (!$this->containers) {
            throw new InvalidPackingScenarioError('There are no containers. Use setContainers or addContainer to add containers');
        }

        $this->sortObjects($this->items);
        $this->sortObjects($this->containers);

        $notPacked = $this->items;

        foreach ($notPacked as $key => $item) {
            foreach ($this->containers as $container) {
                if ($container->addSolid($item)) {
                    unset($notPacked[$key]);
                    break;
                }
            }
        }

        return new PackingResult($this->containers, $notPacked);
    }

    /**
     * Sort items or containers by descending volume, then longest edge.
     *
     * @param $objects
     */
    protected function sortObjects(&$objects)
    {
        usort($objects, function ($a, $b) {
            /** @var SolidInterface $a */
            /** @var SolidInterface $b */
            if ($a->getVolume() === $b->getVolume()) {
                $aDimensions = $a->getSortedDimensionsArray();
                $bDimensions = $b->getSortedDimensionsArray();
                if ($aDimensions[0] === $bDimensions[0]) {
                    return 0;
                }

                return $aDimensions[0] > $bDimensions[0] ? -1 : 1;
            }

            return $a->getVolume() > $b->getVolume() ? -1 : 1;
        });
    }
}