<?php

namespace NAWebCo\BoxPacker;

use JsonSerializable;

class Container implements PackableInterface, SolidInterface, JsonSerializable
{
    use ExtensionTrait;
    use DescribableTrait;
    use ObjectReferenceTrait;

    /**
     * @var ContainerLevel[]
     */
    protected $lowerLevels = [];

    /**
     * @var float
     */
    protected $completeLevelsTotalHeight = 0.0;

    /**
     * @var ContainerLevel
     */
    protected $topLevel;

    /**
     * SolidContainer constructor.
     * @param float $width
     * @param float $length
     * @param float $height
     * @param string $description
     */
    public function __construct($width, $length, $height = 0.0, $description = '')
    {
        $this->setDimensions($width, $length, $height);
        $this->applyStandardOrientation();

        if( $this->getHeight() <= 0 ){
            throw new \InvalidArgumentException('Invalid dimensions provided. All dimensions must be greater than zero.');
        }

        $this->setDescription($description);
        $this->initNewTopLevel();
    }

    /**
     * @param $height
     * @return $this|PackableInterface
     */
    public function setHeight($height): PackableInterface
    {
        if ($height < 0) {
            throw new \InvalidArgumentException(sprintf('Height must be greater than or equal to zero. %f given.', $height));
        }
        $this->height = (float)$height;
        return $this;
    }

    /**
     * @param SolidInterface $solid
     * @return bool
     */
    public function addSolid(SolidInterface $solid)
    {
        if( $solid->getHeight() > $this->getHeight() ){
            return false;
        }

        if (!$this->attemptToAddSolidToLowerLevels($solid)) {
            if (!$this->addSolidToLevel( $solid, $this->topLevel )) {
                if ($this->topLevel->getContentsCount() == 0) {
                    return false;
                }

                // Add another level and try again.
                $this->addNewLevel();
                if (!$this->addSolidToLevel( $solid, $this->topLevel )) {
                    return false;
                }
            }
        }

        return $this->getContentsTotalHeight() <= $this->getHeight();
    }

    /**
     * Remove a solid with a given id from the container (if it exists).
     *
     * @param $id
     * @return bool Indicates whether a solid was found
     */
    public function removeSolid($id)
    {
        foreach( $this->getLevels() as $level ){
            if( $level->removeSolid($id) ){
                return true;
            }
        }
        return false;
    }

    /**
     * @param SolidInterface $solid
     * @param ContainerLevel $level
     * @return bool
     */
    protected function addSolidToLevel(SolidInterface $solid, ContainerLevel $level )
    {
        if( ($result = $level->addSolid($solid)) && $this->getContentsTotalHeight() > $this->getHeight() ){
            $level->removeSolid($solid->getId());
            return false;
        }

        return $result;
    }

    /**
     * Get all the solids packed in this container.
     *
     * @return SolidInterface[]
     */
    public function getContents()
    {
        $solids = [];
        foreach ($this->getLevels() as $level) {
            /** @var ContainerLevel $level */
            $solids = array_merge($solids, array_values($level->getContents()));
        }
        return $solids;
    }

    /**
     * Empty the container. The description is left unchanged.
     * @return $this
     */
    public function emptyContents()
    {
        $this->lowerLevels = [];
        $this->completeLevelsTotalHeight = 0.0;
        $this->topLevel = null;
        $this->initNewTopLevel();
        return $this;
    }

    /**
     * @param SolidInterface $solid
     * @return bool
     */
    protected function attemptToAddSolidToLowerLevels(SolidInterface $solid)
    {
        foreach ($this->lowerLevels as $level) {
            if ($this->addSolidToLevel( $solid, $level)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return $this
     */
    public function sortLowerLevels()
    {
        usort($this->lowerLevels, function ($a, $b) {
            /** @var ContainerLevel $a */
            /** @var ContainerLevel $b */
            if ($a->getContentsMaxHeight() === $b->getContentsMaxHeight()) {
                return 0;
            }

            return $a->getContentsMaxHeight() < $b->getContentsMaxHeight() ? -1 : 1;
        });
        return $this;
    }

    /**
     * @return $this
     */
    public function addNewLevel()
    {
        $this->lowerLevels[] = $this->topLevel;
        $this->completeLevelsTotalHeight += $this->topLevel->getContentsMaxHeight();
        $this->initNewTopLevel();
        return $this;
    }

    /**
     * Initialize new level with the right dimension as the top level).
     * @return $this
     */
    protected function initNewTopLevel()
    {
        $this->topLevel = new ContainerLevel( $this->getWidth(), $this->getLength());
        return $this;
    }

    /**
     * @return array
     */
    public function getLevels()
    {
        return array_merge($this->getLowerLevels(), [$this->topLevel]);
    }

    /**
     * @return array
     */
    protected function getLowerLevels()
    {
        return $this->lowerLevels;
    }

    /**
     * @return int
     */
    public function getContentsCount()
    {
        $count = 0;
        foreach ($this->lowerLevels as $level) {
            /** @var ContainerLevel $level */
            $count += $level->getContentsCount();
        }
        return $count + $this->topLevel->getContentsCount();
    }

    /**
     * @return float
     */
    public function getContentsTotalHeight()
    {
        return $this->topLevel->getContentsMaxHeight() + $this->completeLevelsTotalHeight;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = $this->getDimensions();
        $data['description'] = $this->getDescription();
        $data['contents'] = [];

        foreach($this->getContents() as $solid){
            $data['contents'][] = $solid->toArray();
        }

        return $data;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
