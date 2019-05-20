<?php
namespace NAWebCo\BoxPackerTest;

use PHPUnit\Framework\TestCase;

use NAWebCo\BoxPacker\Solid;
use NAWebCo\BoxPacker\Container;
use NAWebCo\BoxPacker\Packer;

class PackerTest extends TestCase
{

    /**
     * @expectedException \TypeError
     */
    public function testSetInvalidBoxes()
    {
        $container = 'dummy';
        $packer = new Packer();
        $packer->setBoxes([$container]);
    }

    /**
     * @expectedException \TypeError
     */
    public function testSetInvalidItems()
    {
        $packer = new Packer();
        $packer->setItems([new Solid(1,1,1), 'Just some random text']);
    }

    public function testPackerMinimizesContainers()
    {
        $items = [
            new Solid(1, 1, 1 ),
            new Solid(1, 1, 1 ),
            new Solid(2, 2, 1 ),
        ];

        $containers = [
            new Container(1, 1, 1),
            new Container(2, 1, 1),
            new Container(2, 2, 1),
        ];

        $packer = new Packer($containers, $items);
        $result = $packer->pack();

        $this->assertCount(2, $result->getPackedBoxes());
    }

    /**
     * @expectedException  \NAWebCo\BoxPacker\InvalidPackingScenarioError
     */
    public function testPackWithoutItems()
    {
        $packer = new Packer([new Container(4, 4, 4)], []);
        $packer->pack();
    }

    /**
     * @expectedException  \NAWebCo\BoxPacker\InvalidPackingScenarioError
     */
    public function testPackWithoutContainers()
    {
        $packer = new Packer([], [new Solid(1,1,1)]);
        $packer->pack();
    }

}