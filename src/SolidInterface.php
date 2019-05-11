<?php


namespace NAWebCo\BoxPacker;


interface SolidInterface
{

    public function getWidth();

    public function getLength();

    public function getHeight();

    public function getVolume();

    public function getSortedDimensionsArray();

    public function applyStandardOrientation();

    public function canContain(SolidInterface $solid);

    public function canContainBaseWithoutXOrYAxisRotation(SolidInterface $solid);

    public function rotateX();

    public function rotateY();

    public function rotateZ();

    public function toArray();
}