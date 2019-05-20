<?php


namespace NAWebCo\BoxPacker;


trait ObjectReferenceTrait
{

    /**
     * @var mixed
     */
    protected $objectReference;

    /**
     * @param $object
     * @return $this
     */
    public function setObjectReference($object)
    {
        $this->objectReference = $object;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjectReference()
    {
        return $this->objectReference;
    }
}