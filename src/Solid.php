<?php
namespace NAWebCo\BoxPacker;

use \JsonSerializable;

class Solid implements SolidInterface, JsonSerializable
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
        $this->setId(uniqid());
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
     * @return array
     */
    public function toArray()
    {
        $data = $this->getDimensions();
        $data['description'] = $this->getDescription();

        return $data;
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