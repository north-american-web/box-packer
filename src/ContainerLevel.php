<?php
namespace NAWebCo\BoxPacker;

use \InvalidArgumentException;

class ContainerLevel
{
    use ExtensionTrait;

    /**
     * @var Container[]
     */
    protected $containers = [];

    /**
     * @var float
     */
    protected $contentsMaxHeight = 0.0;

    /**
     * @var SolidInterface[]
     */
    protected $packedSolids = [];

    /**
     * @var SolidInterface[]
     */
    protected $spaces = [];

    /**
     * ContainerLevel constructor.
     * @param float $width
     * @param float $length
     */
    public function __construct($width, $length)
    {
        $this->width = (float)$width;
        $this->length = (float)$length;

        if( ($width * $length) <= 0 ){
            throw new \InvalidArgumentException( sprintf('Dimensions must both be greater than 0. %f, %f provided.', $width, $length ));
        }

        $this->spaces = [new Solid($width, $length)];
    }

    /**
     * @param SolidInterface $solid
     * @return bool
     */
    public function addSolid(SolidInterface $solid)
    {
        if( $this->containers ){
            return $this->attemptToAddToContainers($solid);
        }

        return $this->attemptToAddToSpaces($solid);
    }

    /**
     * @return int
     */
    public function getContentsCount()
    {
        $count = count($this->packedSolids);
        if( $this->containers ){
            foreach( $this->containers as $container ){
                /** @var Container $container */
                $count += $container->getContentsCount();
            }
        }

        return $count;
    }

    /**
     * @return float
     */
    public function getContentsMaxHeight()
    {
        return $this->contentsMaxHeight;
    }

    /**
     * Get all the solids packed in this layer (including in containers)
     *
     * @return SolidInterface[]
     */
    public function getPackedSolids()
    {
        $packed = $this->packedSolids;
        if( $this->containers ){
            foreach( $this->containers as $container ){
                /** @var Container $container */
                $packed = array_merge( $packed, $container->getPackedSolids());
            }
        }

        return $packed;
    }

    /**
     * Given a two-dimensional open area and a solid, return the area(s) that would remain if the solid were placed in
     * the area. The solid will rest along (at least) two edges of the area and will be oriented to yield the largest
     * possible remaining space.
     *
     * @param SolidInterface $item
     * @param SolidInterface $area
     * @return array
     * @throws InvalidArgumentException
     */
    public function calculateNewSpaces(SolidInterface $item, SolidInterface $area)
    {
        if( !$area->canContainBaseWithoutXOrYAxisRotation($item) ){
            throw new InvalidArgumentException('Item cannot fit in the container.');
        }

        // If the solid's width (its longest edge) can fit against the area's length (its shortest edge),
        // then that orientation will yield a larger remaining area.
        if ($item->getWidth() <= $area->getLength()) {
            $item->rotateZ();
        }

        $widthDiff = $area->getWidth() - $item->getWidth();
        $lengthDiff = $area->getLength() - $item->getLength();

        // Item exactly fills the given space.
        if ($widthDiff == 0 && $lengthDiff == 0) {
            return [];
        }

        if( $widthDiff == 0 ){
            return [new Solid($lengthDiff, $item->getWidth())];
        }

        if ( $lengthDiff == 0) {
            return [new Solid($widthDiff, $item->getLength())];
        }

        return [
            new Solid( $lengthDiff, $area->getWidth()),
            new Solid( $widthDiff, $item->getLength())
        ];
    }

    /**
     * @param SolidInterface $solid
     * @return bool
     */
    protected function attemptToAddToContainers(SolidInterface $solid)
    {
        if( !$this->containers ){
            return false;
        }

        foreach( $this->containers as $container ){
            if( $container->addSolid($solid) ){
                return true;
            }
        }
        return false;
    }

    /**
     * @param SolidInterface $solid
     * @return bool
     */
    protected function attemptToAddToSpaces(SolidInterface $solid)
    {
        $viableSpaceKey = $this->getKeyOfSmallestViableSpace($solid);

        if( $viableSpaceKey === null ){
            if( count($this->packedSolids) === 0 ){
                return false;
            }

            // Create containers and try again
            if( !$this->containers ){
                $this->initContainers();

                if( !$this->containers ){
                    // There were no spaces (including above packed items) to make into containers.
                    return false;
                }
                return $this->addSolid($solid);
            }

            return false;
        }

        $this->contentsMaxHeight = max($solid->getHeight(), $this->contentsMaxHeight);
        $this->placeSolidInSpace($solid, $viableSpaceKey);

        return true;
    }

    /**
     * Make containers out of remaining spaces and areas above placed solids. All subsequent attempts to add a
     * solid to this level will
     */
    protected function initContainers()
    {
        $this->containers = $this->getSpacesAbovePlacedSolidsAsContainers();
        foreach( $this->spaces as $space ){
            $this->containers[] = new Container(
                $space->getWidth(),
                $space->getLength(),
                $this->getContentsMaxHeight()
            );
        }
        $this->spaces = [];
        $this->sortSolids($this->containers);
    }

    /**
     * @param SolidInterface $solid
     * @return int|null
     */
    protected function getKeyOfSmallestViableSpace(SolidInterface $solid)
    {
        if( !$this->spaces ){
            return null;
        }

        foreach ($this->spaces as $key => $space) {
            /**
             * @var SolidInterface $space
             */
            if ($space->canContainBaseWithoutXOrYAxisRotation($solid)) {
                return $key;
            }
        }
        return null;
    }

    /**
     * @param SolidInterface $solid
     * @param int $spaceKey
     * @throws InvalidArgumentException
     */
    protected function placeSolidInSpace(SolidInterface $solid, $spaceKey)
    {
        if (!array_key_exists($spaceKey, $this->spaces)) {
            throw new InvalidArgumentException('Invalid open area key.');
        }

        // Rework open areas
        $space = $this->spaces[$spaceKey];
        unset($this->spaces[$spaceKey]);
        $this->spaces = array_merge($this->spaces, $this->calculateNewSpaces($solid, $space));
        $this->sortSolids($this->spaces);

        $this->packedSolids[] = $solid;
    }

    /**
     * Get the areas above each placed solid on this level as SolidContainers.
     *
     * @return ContainerLevel[]
     */
    protected function getSpacesAbovePlacedSolidsAsContainers()
    {
        $spaces = [];
        foreach($this->packedSolids as $solid ){
            $openHeight = $this->getContentsMaxHeight() - $solid->getHeight();
            if( $openHeight > 0 ){
                $spaces[] = new Container(
                    $solid->getWidth(),
                    $solid->getLength(),
                    $openHeight
                );
            }
        }
        return $spaces;
    }

    /**
     * Sort spaces by width (smallest to largest). If widths are equal, sort by length (smallest to largest).
     * @param $solids
     */
    protected function sortSolids( &$solids )
    {
        usort($solids, function ($a, $b) {
            /** @var SolidInterface $a */
            /** @var SolidInterface $b */
            if ($a->getWidth() == $b->getWidth()) {
                if ($a->getLength() == $b->getLength()) {
                    return 0;
                }
                return $a->getLength() < $b->getLength() ? -1 : 1;
            }
            return $a->getWidth() < $b->getWidth() ? -1 : 1;
        });
    }

}