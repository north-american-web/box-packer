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
     * @param $width
     * @param $length
     * @param int $height
     * @return $this
     */
    protected function setDimensions($width, $length, $height = 0.0)
    {
        $this->setWidth($width);
        $this->setLength($length);
        $this->setHeight($height);
        return $this;
    }

    /**
     * @param float $height
     * @return ExtensionTrait
     */
    protected function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param float $length
     * @return ExtensionTrait
     */
    protected function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @param float $width
     * @return ExtensionTrait
     */
    protected function setWidth($width)
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
     * Make longest dimension width, middle dimension length, and shortest dimension height.
     *
     * @return $this
     */
    public function applyStandardOrientation()
    {
        $dimensions = $this->getSortedDimensionsArray();

        $this->width = $dimensions[0];
        $this->length = $dimensions[1];
        $this->height = $dimensions[2];

        return $this;
    }

    /**
     * Get an array of this solid's dimensions, sorted from high to low.
     *
     * @return array
     */
    public function getSortedDimensionsArray()
    {
        $dimensions = [$this->getWidth(), $this->getLength(), $this->getHeight()];
        rsort($dimensions);
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