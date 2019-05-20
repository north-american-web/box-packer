<?php


namespace NAWebCo\BoxPacker;


class PackingResult
{

    /**
     * @var Container[]
     */
    protected $containers = [];

    /**
     * @var Container[]
     */
    protected $emptyContainers = [];

    /**
     * @var Container[]
     */
    protected $packedContainers = [];

    /**
     * @var Solid[]
     */
    protected $notPackedItems = [];

    /**
     * PackingResult constructor.
     * @param array $packedContainers
     * @param array $notPackedItems
     */
    public function __construct(array $packedContainers, array $notPackedItems)
    {
        foreach ($packedContainers as $container) {
            if (!($container instanceof Container)) {
                throw new \InvalidArgumentException('Invalid $packedContainers argument. Expected array of containers.');
            }
        }

        foreach ($notPackedItems as $item) {
            if (!($item instanceof Solid)) {
                throw new \InvalidArgumentException('Unrecognized $notPackedItems type. Solid expected.');
            }
        }

        $this->notPackedItems = $notPackedItems;
        $this->containers = $packedContainers;

        $this->sortOutPackedAndEmptyContainers();
    }

    /**
     * @return $this
     */
    protected function sortOutPackedAndEmptyContainers()
    {
        foreach($this->containers as $container){
            $container->getContentsCount() > 0
                ? $this->packedContainers[] = $container
                : $this->emptyContainers[] = $container;
        }
        return $this;
    }

    /**
     * @param Solid{}|Container[] $things
     * @param bool $includeContents
     * @return array
     */
    protected function getObjectReferencesFromSolidsOrContainers($things, $includeContents = false)
    {
        if( !$things ){
            return [];
        }


        return array_map(function($container) use ($includeContents){
            /** @var Container $container */

            if( $includeContents ){
                return [
                    'box' => $container->getObjectReference(),
                    'contents' => array_map(function($solid){
                            return $solid->getObjectReference();
                        }, $container->getContents()
                    )
                ];
            }
            return $container->getObjectReference();
        }, $things);
    }

    /**
     * @param bool $includeContents
     * @return array
     */
    public function getPackedBoxes($includeContents = true)
    {
        return $this->getObjectReferencesFromSolidsOrContainers($this->packedContainers, $includeContents);
    }

    /**
     * @param bool $includeContents
     * @return array
     */
    public function getEmptyBoxes($includeContents = true)
    {
        return $this->getObjectReferencesFromSolidsOrContainers($this->emptyContainers, $includeContents);
    }

    /**
     * @return array
     */
    public function getNotPackedItems()
    {
        return $this->getObjectReferencesFromSolidsOrContainers($this->notPackedItems);
    }

    /**
     * Was the packing successful (i.e. did all the items fit into the containers)?
     *
     * @return bool
     */
    public function success()
    {
        return count($this->notPackedItems) === 0;
    }
}