<?php

namespace NAWebCo\BoxPackerTest;

use PHPUnit\Framework\TestCase;

use NAWebCo\BoxPacker\Solid;
use NAWebCo\BoxPacker\Container;
use NAWebCo\BoxPacker\ContainerLevel;
use Prophecy\Exception\InvalidArgumentException;

class ContainerTest extends TestCase
{

    public function testAddOneViableSolid()
    {
        $container = new Container(4,4,4);

        $result = $container->addSolid(new Solid(2, 2, 2));

        $this->assertTrue($result);
        $this->assertEquals(2.0, $container->getContentsTotalHeight());
    }

    public function testGetContentsCount()
    {
        $container = new Container(2,2,2);

        // This will require 2 levels
        $container->addSolid(new Solid(2,2,1));
        $container->addSolid(new Solid(1,1,1));
        $container->addSolid(new Solid(1,1,1));

        $this->assertEquals(3, $container->getContentsCount());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidDimensions()
    {
        new Container(1,1,0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidHeight()
    {
        $container = new Container(1,1,1);
        $container->setHeight(-1);
    }

    public function testToArrayWithStackedItems()
    {
        $container = new Container(2,2,2, 'container');
        $solid1 = new Solid(2,2,1, 'box 1');
        $solid2 = new Solid(2,2,1, 'box 2');
        $container->addSolid($solid1);
        $container->addSolid($solid2);

        $expected = [
            'width' => 2.0,
            'length' => 2.0,
            'height' => 2.0,
            'description' => 'container',
            'contents' => [
                [ 'width' => 2.0, 'length' => 2.0, 'height' => 1.0, 'description' => 'box 1'  ],
                [ 'width' => 2.0, 'length' => 2.0, 'height' => 1.0, 'description' => 'box 2'  ]
            ]
        ];

        $this->assertEquals($expected, $container->toArray());
    }


//    public function testSortLevels()
//    {
//        /** @var SolidContainer $container */
//        $container = new Container(5, 5, 10);
//        $levelMock =  $this->getMockBuilder(ContainerLevel::class)
//            ->disableOriginalConstructor()
//            ->setMethods(['addSolid', 'attemptToAddContainers','attemptToAddToSpaces'])->getMock();
//        $levelMock->expects($this->once())->method('addSolid')->willReturn(true);
//
//        $container->addSolid(new Solid(5,5,2));
//        $container->addSolid(new Solid(5,5,1));
//        $container->addSolid(new Solid(5,5,3));
//        $container->addNewLevel();
//        $container->sortLowerLevels();
//
//        $levels = $container->getLowerLevels();
//        $heights = [];
//        foreach( $levels as $level ){
//            /** @var ContainerLevel $level */
//            $heights[] = $level->getContentsMaxHeight();
//        }
//
//        $this->assertEquals([1.0, 2.0, 3.0], $heights);
//
//    }


}