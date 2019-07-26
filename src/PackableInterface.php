<?php


namespace NAWebCo\BoxPacker;


interface PackableInterface
{

    public function getDescription();

    public function setDescription(string $description): PackableInterface;

    public function getHeight();

    public function setHeight($height): PackableInterface;

    public function getLength();

    public function setLength($length): PackableInterface;

    public function getWidth();

    public function setWidth($width): PackableInterface;

}
