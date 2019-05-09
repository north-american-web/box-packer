<?php
namespace NAWebCo\BoxPacker;

class Solid
{
    use ExtensionTrait;
    use DescribableTrait;

    /**
     * A name or id for this item.
     *
     * @var string
     */
    protected $description;

    /**
     * NAWebCo_ShippingRules_Solid constructor.
     * @param float $width
     * @param float $length
     * @param float $height
     * @param null $description
     */
    public function __construct($width, $length, $height = 0.0, $description = null)
    {
        $this->setDimensions( (float)$width, (float)$length, (float)$height);
        $this->description = $description;

        $this->applyStandardOrientation();

        if ($this->height < 0) {
            throw new \InvalidArgumentException('One or more dimensions is less than zero.');
        }

        if ($this->height == 0 && $this->length <= 0) {
            throw new \InvalidArgumentException('Two or more dimensions are less than or equal to zero.');
        }
    }

    /**
     * Test whether this solid can contains another given one.
     *
     * @param Solid $solid
     * @return bool
     */
    public function canContain(Solid $solid)
    {
        $thisDimensions = $this->getSortedDimensionsArray();
        $solidDimensions = $solid->getSortedDimensionsArray();

        return $thisDimensions[0] >= $solidDimensions[0]
            && $thisDimensions[1] >= $solidDimensions[1]
            && $thisDimensions[2] >= $solidDimensions[2];
    }

    /**
     * Test whether a solid's base can fit within this solid's base without reorientation on the x or y axis.
     *
     * @param Solid $solid
     * @return bool
     */
    public function canContainBaseWithoutXOrYAxisRotation(Solid $solid)
    {
        return ($this->getWidth() >= $solid->getWidth() && $this->getLength() >= $solid->getLength())
            || ($this->getLength() >= $solid->getWidth() && $this->getWidth() >= $solid->getLength());
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $reference
     * @return $this
     */
    public function setDescription($reference)
    {
        $this->description = $reference;
        return $this;
    }
}