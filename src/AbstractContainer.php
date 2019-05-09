<?php
namespace NAWebCo\BoxPacker;

class AbstractContainer
{
    use ExtensionTrait;

    /**
     * NAWebCo_ShippingRules_SolidContainer constructor.
     * @param Solid $solid
     */
    public function __construct(Solid $solid )
    {
        $this->setDimensionsBySolid($solid);
    }

    /**
     * @param $width
     * @param $length
     * @param $height
     * @return AbstractContainer
     */
    public static function createFromDimensions( $width, $length, $height )
    {
        $solid = new Solid($width, $length, $height);
        return new static($solid);
    }

    /**
     * @return Solid
     */
    public function getDimensionsAsSolid()
    {
        return new Solid($this->getWidth(), $this->getLength(), $this->getHeight());
    }

    /**
     * @param Solid $solid
     */
    protected function setDimensionsBySolid(Solid $solid )
    {
        $this->setDimensions($solid->getWidth(), $solid->getLength(), $solid->getHeight());
        $this->applyStandardOrientation();

        if( $this->getHeight() == 0 ){
            throw new \InvalidArgumentException('Invalid dimensions provided. All dimensions must be greater than zero.');
        }
    }


}