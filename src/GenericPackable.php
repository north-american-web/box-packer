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
    public function __construct($width = null, $length = null, $height = null, $description = null)
    {
        $this->setWidth($width)
            ->setLength($length)
            ->setHeight($height)
            ->setDescription($description);
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

    /**
     * @param $description
     * @return $this|PackableInterface
     */
    public function setDescription(string $description): PackableInterface
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param $height
     * @return PackableInterface
     */
    public function setHeight($height): PackableInterface
    {
        $this->height = (float) $height;
        return $this;
    }

    /**
     * @param $length
     * @return PackableInterface
     */
    public function setLength($length): PackableInterface
    {
        $this->length = (float) $length;
        return $this;
    }

    /**
     * @param $width
     * @return PackableInterface
     */
    public function setWidth($width): PackableInterface
    {
        $this->width = $width;
        return $this;
    }
}
