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
    }

    /**
     * @return array
     */
    public function getContainers()
    {
        $containers = [];
        foreach($this->containers as $container){
            if( $container->getContentsCount() > 0 ){
                $containers[] = $container;
            }
        }

        return $containers;
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
     * @return array
     */
    public function getPackedContainers()
    {
        if( empty($this->packedContainers) && empty($this->emptyContainers) ){
            $this->sortOutPackedAndEmptyContainers();
        }
        return $this->packedContainers;
    }

    /**
     * @return array
     */
    public function getEmptyContainers()
    {
        if( empty($this->packedContainers) && empty($this->emptyContainers) ){
            $this->sortOutPackedAndEmptyContainers();
        }
        return $this->emptyContainers;
    }

    /**
     * @return array
     */
    public function getNotPackedItems()
    {
        return $this->notPackedItems;
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