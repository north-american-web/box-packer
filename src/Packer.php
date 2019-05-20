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
    protected $solids = [];

    /**
     * Packer constructor.
     * @param Container|Container[] $boxes
     * @param SolidInterface[] $items
     */
    public function __construct($boxes = [], $items = [])
    {
        $this->setBoxes($boxes);
        $this->setItems($items);
    }

    /**
     * @param PackableInterface[] $boxes
     * @return $this
     */
    public function setBoxes($boxes)
    {
        if (is_array($boxes)) {
            foreach ($boxes as $container) {
                $this->addBox($container);
            }
        } else {
            $this->addBox($boxes);
        }

        return $this;
    }

    /**
     * @param PackableInterface $box
     * @return $this
     */
    public function addBox(PackableInterface $box)
    {
        $this->containers[] = $this->getBoxContainer($box);
        return $this;
    }

    /**
     * @param PackableInterface $box
     * @return Container
     */
    protected function getBoxContainer(PackableInterface $box)
    {
        $container = new Container( $box->getWidth(), $box->getLength(), $box->getHeight(), $box->getDescription());
        $container->setId(uniqid());
        $container->setObjectReference($box);
        return $container;
    }

    /**
     * @param PackableInterface|PackableInterface[] $items
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
     * @param PackableInterface $item
     * @return $this
     */
    public function addItem(PackableInterface $item)
    {
        $this->solids[] = $this->getItemSolid($item);
        return $this;
    }

    /**
     * Get a Solid representation of a packable item
     *
     * @param PackableInterface $item
     * @return PackableInterface|Solid
     */
    protected function getItemSolid(PackableInterface $item)
    {
        $solid = new Solid( $item->getWidth(), $item->getLength(), $item->getHeight(), $item->getDescription());
        $solid->setId(uniqid());
        $solid->setObjectReference($item);
        return $solid;
    }

    /**
     * @return PackingResult
     * @throws InvalidPackingScenarioError
     */
    public function pack()
    {
        if (!$this->solids) {
            throw new InvalidPackingScenarioError('There are no items to pack. Use setItems or addItem to add items.');
        }

        if (!$this->containers) {
            throw new InvalidPackingScenarioError('There are no containers. Use setContainers or addContainer to add containers');
        }

        $this->sortObjects($this->solids);
        $this->sortObjects($this->containers);

        $notPacked = $this->solids;

        foreach ($notPacked as $key => $solid) {
            foreach ($this->containers as $container) {
                if ($container->addSolid($solid)) {
                    unset($notPacked[$key]);
                    break;
                }
            }
        }

        return new PackingResult($this->containers, array_values($notPacked));
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
                if ($aDimensions['width'] === $bDimensions['width']) {
                    return 0;
                }

                return $aDimensions['width'] > $bDimensions['width'] ? -1 : 1;
            }

            return $a->getVolume() > $b->getVolume() ? -1 : 1;
        });
    }
}