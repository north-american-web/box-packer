<?php


namespace NAWebCo\BoxPackerTest;

use NAWebCo\BoxPacker\GenericPackable;
use PHPUnit\Framework\TestCase;

use NAWebCo\BoxPacker\Solid;
use NAWebCo\BoxPacker\Container;
use NAWebCo\BoxPacker\PackingResult;

class PackingResultTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidContainer()
    {
        $containers = [
            new Container(1,1,1),
            'Bad item'
        ];

        new PackingResult($containers, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidItem()
    {
        $items = [
            new Solid(1,1,1),
            'Bad item'
        ];

        new PackingResult([], $items);
    }

    public function testGetPackedAndEmptyContainers()
    {
        $fullBox = new GenericPackable(1,1,1, 'full box');
        $emptyBox = new GenericPackable(1,1,1, 'empty box');

        $fullContainer = $this->createMock(Container::class);
        $fullContainer->method('getObjectReference')->willReturn($fullBox);
        $fullContainer->method('getContentsCount')->willReturn(2);

        $emptyContainer = $this->createMock(Container::class);
        $emptyContainer->method('getObjectReference')->willReturn($emptyBox);
        $emptyContainer->method('getContentsCount')->willReturn(0);

        $packingResult = new PackingResult([$fullContainer, $emptyContainer], []);

        $this->assertEquals([$fullBox], $packingResult->getPackedBoxes(false));
        $this->assertEquals([$emptyBox], $packingResult->getEmptyBoxes(false));
    }

    public function testGetPackedBoxesIncludeContents()
    {
        $fullBox = new GenericPackable(1,1,1, 'full box');
        $item = new GenericPackable(1,1,1,'item');

        $solid = new Solid(1,1,1);
        $solid->setObjectReference($item);

        $fullContainer = $this->createMock(Container::class);
        $fullContainer->method('getObjectReference')->willReturn($fullBox);
        $fullContainer->method('getContents')->willReturn([$solid]);
        $fullContainer->method('getContentsCount')->willReturn(1);

        $packingResult = new PackingResult([$fullContainer], []);

        $expected = [[
            'box' => $fullBox,
            'contents' => [$item]
        ]];

        $this->assertEquals($expected, $packingResult->getPackedBoxes(true));
    }

    public function testGetNotPackedItems()
    {
        $item = new GenericPackable(1,2,3);
        $solid = (new Solid(1,1,1))->setObjectReference($item);
        $packingResult = new PackingResult([], [$solid]);

        $this->assertEquals([$item], $packingResult->getNotPackedItems());
    }

}