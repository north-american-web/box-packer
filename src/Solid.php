<?php
namespace NAWebCo\BoxPacker;

class Solid implements SolidInterface
{
    use ExtensionTrait;
    use DescribableTrait;

    /**
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

}