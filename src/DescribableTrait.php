<?php


namespace NAWebCo\BoxPacker;


trait DescribableTrait
{
    /**
     * A name or id for this item.
     *
     * @var string
     */
    protected $description;

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}