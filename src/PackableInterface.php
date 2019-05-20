<?php


namespace NAWebCo\BoxPacker;


interface PackableInterface
{

    public function getDescription();

    public function getHeight();

    public function getLength();

    public function getWidth();

}