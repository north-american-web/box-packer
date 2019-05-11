<?php

namespace NAWebCo\BoxPackerTest;

use PHPUnit\Framework\TestCase;

use NAWebCo\BoxPacker\Solid;

class SolidTest extends TestCase
{

    /**
     * @dataProvider invalidDimensions
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidDimensions( $width, $length, $height )
    {
        new Solid($width, $length, $height);
    }

    public function invalidDimensions()
    {
        return [
            [ 0, 0, 0 ],
            [ 1, 0, 0 ],
            [ 3, 2, -1]
        ];
    }

    /**
     * @dataProvider orientationsData
     */
    public function testApplyStandardOrientation( $width, $length, $height ){

        $solid = new Solid($width, $length, $height);
        $solid->applyStandardOrientation();

        $this->assertGreaterThanOrEqual( $solid->getLength(), $solid->getWidth());
        $this->assertGreaterThanOrEqual( $solid->getHeight(), $solid->getLength());
    }

    public function orientationsData()
    {
        return [
            [ 1, 2, 3],
            [ 3, 2, 1]
        ];
    }

    public function testGetSortedDimensionsArray()
    {
        $solid = new Solid(3,2,1);
        $solid->rotateX();

        $expected = [
            'width' => 3.0,
            'height' => 2.0,
            'length' => 1.0
        ];

        $this->assertEquals($expected, $solid->getSortedDimensionsArray());
    }

    /**
     * @param $s1Width
     * @param $s1Length
     * @param $s1Height
     * @param $s2Width
     * @param $s2Length
     * @param $s2Height
     * @param $expected
     * @throws Exception
     * @dataProvider canContainData
     */
    public function testCanContain($s1Width, $s1Length, $s1Height, $s2Width, $s2Length, $s2Height, $expected )
    {
        $solid = new Solid($s1Width, $s1Length, $s1Height);
        $container = new Solid($s2Width, $s2Length, $s2Height);

        $actual = $container->canContain($solid);

        $this->assertEquals($expected, $actual);
    }

    public function canContainData()
    {
        return [
            // Typical
            [1,1,1, 2,2,2, true],
            [2,2,1, 2,2,2, true],
            [2,2,2, 2,2,2, true],

            // Oversize
            [3,1,1, 2,2,2, false],

            // Weird cases
            [1,1,0, 2,2,0, true],
            [1,1,1, 2,2,0, false],
        ];
    }

    /**
     * @param $itemWidth
     * @param $itemLength
     * @param $containerWidth
     * @param $containerLength
     * @dataProvider willBaseFitData
     * @throws Exception
     */
    public function testCanContainBaseWithoutXorYAxisRotation($itemWidth, $itemLength, $containerWidth, $containerLength, $expected)
    {
        $solid = new Solid($itemWidth, $itemLength);
        $container = new Solid($containerWidth, $containerLength);

        $actual = $container->canContainBaseWithoutXOrYAxisRotation($solid);

        $this->assertEquals($expected, $actual);
    }

    public function willBaseFitData()
    {
        return [
            [1,2, 2,2, true],
            [2,2, 2,2, true],
            [3,1, 1,4, true],
            [1,3, 2,2, false],
            [3,1, 2,2, false],
            [3,3, 2,2, false],
        ];
    }

    public function testRotateX()
    {
        $solid = new Solid( 3, 2, 1 );
        $solid->rotateX();

        $this->assertEquals([3,1,2], [$solid->getWidth(), $solid->getLength(), $solid->getHeight()]);
    }

    public function testRotateY()
    {
        $solid = new Solid( 3, 2, 1 );
        $solid->rotateY();

        $this->assertEquals([1,2,3], [$solid->getWidth(), $solid->getLength(), $solid->getHeight()]);
    }

    public function testRotateZ()
    {
        $solid = new Solid( 3, 2, 1 );
        $solid->rotateZ();

        $this->assertEquals([2,3,1], [$solid->getWidth(), $solid->getLength(), $solid->getHeight()]);
    }

    public function testWithoutDescriptionToArray()
    {
        $expected = [
            'width' => 3.0,
            'length' => 2.0,
            'height' => 1.0,
            'description' => null
        ];

        $solid = new Solid($expected['width'], $expected['length'], $expected['height']);

        $this->assertEquals($expected, $solid->toArray());
    }

    public function testWithDescriptionToArray()
    {
        $expected = [
            'width' => 3.0,
            'length' => 2.0,
            'height' => 1.0,
            'description' => 'description string'
        ];

        $solid = new Solid($expected['width'], $expected['length'], $expected['height'], $expected['description']);

        $this->assertEquals($expected, $solid->toArray());
    }

}