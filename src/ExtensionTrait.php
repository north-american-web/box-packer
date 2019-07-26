<?php


namespace NAWebCo\BoxPacker;


trait ExtensionTrait
{

    /**
     * @var float
     */
    protected $height;

    /**
     * @var float
     */
    protected $length;

    /**
     * @var float
     */
    protected $width;

    /**
     * @return array
     */
    protected function getDimensions()
    {
        return [
            'width' => $this->getWidth(),
            'length' => $this->getLength(),
            'height' => $this->getHeight(),
        ];
    }

    /**
     * @param $width
     * @param $length
     * @param float $height
     * @return $this
     */
    public function setDimensions($width, $length, $height = 0.0)
    {
        $this->setWidth($width);
        $this->setLength($length);
        $this->setHeight($height);
        return $this;
    }

    /**
     * @param float $height
     * @return PackableInterface
     */
    public function setHeight($height): PackableInterface
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param float $length
     * @return PackableInterface
     */
    public function setLength($length): PackableInterface
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @param float $width
     * @return PackableInterface
     */
    public function setWidth($width): PackableInterface
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getVolume()
    {
        return $this->getWidth() * $this->getLength() * $this->getHeight();
    }

    /**
     * Test whether this solid can contains another given one.
     *
     * @param SolidInterface $solid
     * @return bool
     */
    public function canContain(SolidInterface $solid)
    {
        $thisDimensions = $this->getSortedDimensionsArray();
        $solidDimensions = $solid->getSortedDimensionsArray();

        return $thisDimensions['width'] >= $solidDimensions['width']
            && $thisDimensions['length'] >= $solidDimensions['length']
            && $thisDimensions['height'] >= $solidDimensions['height'];
    }

    /**
     * Test whether a solid's base can fit within this solid's base without reorientation on the x or y axis.
     *
     * @param SolidInterface $solid
     * @return bool
     */
    public function canContainBaseWithoutXOrYAxisRotation(SolidInterface $solid)
    {
        return ($this->getWidth() >= $solid->getWidth() && $this->getLength() >= $solid->getLength())
            || ($this->getLength() >= $solid->getWidth() && $this->getWidth() >= $solid->getLength());
    }

    /**
     * Make longest dimension width, middle dimension length, and shortest dimension height.
     *
     * @return $this
     */
    public function applyStandardOrientation()
    {
        $dimensions = $this->getSortedDimensionsArray();

        $this->width = current($dimensions);
        $this->length = next($dimensions);
        $this->height = next($dimensions);

        return $this;
    }

    /**
     * Get an array of this solid's dimensions, sorted from high to low.
     *
     * @return array
     */
    public function getSortedDimensionsArray()
    {
        $dimensions = $this->getDimensions();
        arsort($dimensions);
        return $dimensions;
    }

    /**
     * Rotate 90 degrees on the X axis (swap length and height values).
     *
     * @return $this
     */
    public function rotateX()
    {
        $oldLength = $this->length;
        $this->length = $this->height;
        $this->height = $oldLength;
        return $this;
    }

    /**
     * Rotate 90 degrees on the Y axis (swap width and height values).
     *
     * @return $this
     */
    public function rotateY()
    {
        $oldHeight = $this->height;
        $this->height = $this->width;
        $this->width = $oldHeight;
        return $this;
    }

    /**
     * Rotate 90 degrees on the Z axis (swap width and length values).
     *
     * @return $this
     */
    public function rotateZ()
    {
        $oldWidth = $this->width;
        $this->width = $this->length;
        $this->length = $oldWidth;
        return $this;
    }

}
