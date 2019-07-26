<?php


namespace NAWebCo\BoxPacker;


use JsonSerializable;

class GenericPackable implements PackableInterface, JsonSerializable
{
    use DescribableTrait;
    use ExtensionTrait;

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
