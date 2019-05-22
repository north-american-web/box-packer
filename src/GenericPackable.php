<?php


namespace NAWebCo\BoxPacker;


use JsonSerializable;

class GenericPackable implements PackableInterface, JsonSerializable
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

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'width' => $this->getWidth(),
            'length' => $this->getLength(),
            'height' => $this->getHeight(),
            'description' => $this->getDescription()
        ];
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