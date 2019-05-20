<?php


namespace NAWebCo\BoxPacker;


class GenericPackable implements PackableInterface
{

    /**
     * @var null
     */
    protected $description;

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
     * GenericItem constructor.
     * @param $width
     * @param $length
     * @param $height
     * @param null $description
     */
    public function __construct($width, $length, $height, $description = null)
    {
        $this->width = (float) $width;
        $this->length = (float) $length;
        $this->height = (float) $height;
        $this->description = $description;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }


}