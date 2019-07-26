<?php


namespace NAWebCo\BoxPacker;


trait DescribableTrait
{

    /**
     * A description of this item.
     *
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this|PackableInterface
     */
    public function setDescription(?string $description): PackableInterface
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return DescribableTrait
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }



}
