<?php

namespace NAWebCo\BoxPackerTest;

use PHPUnit\Framework\TestCase;

use NAWebCo\BoxPacker\Solid;
use NAWebCo\BoxPacker\Container;
use NAWebCo\BoxPacker\ContainerLevel;

class ContainerTest extends TestCase
{

    public function testAddOneViableSolid()
    {
        $levelMock =  $this->getMockBuilder(ContainerLevel::class)
            ->disableOriginalConstructor()
            ->setMethods(['addSolid', 'getContentsMaxHeight'])->getMock();
        $levelMock->expects($this->once())->method('addSolid')->willReturn(true);
        $levelMock->expects($this->any())->method('getContentsMaxHeight')->willReturn(2.0);

        $container = new Container(4,4,4);
        $container->setLevelPrototype($levelMock);

        $result = $container->addSolid(new Solid(2, 2, 2));

        $this->assertTrue($result);
        $this->assertEquals(2.0, $container->getContentsTotalHeight());
    }

    public function testGetContentsCount()
    {
        $iterations = 5;

        for( $expectedCount = 1; $expectedCount <= $iterations; $expectedCount++ ){
            $levelMock =  $this->getMockBuilder(ContainerLevel::class)
                ->disableOriginalConstructor()
                ->setMethods(['addSolid', 'getContentsCount'])->getMock();
            $levelMock->expects($this->any())->method('addSolid')->willReturn(true);
            $levelMock->expects($this->once())->method('getContentsCount')->willReturn($expectedCount);

            $container = new Container(4,4,4);
            $container->setLevelPrototype($levelMock);

            for( $n = 0; $n <= $expectedCount; $n++ ){
                $container->addSolid(new Solid(1,1,1));
            }

            $this->assertEquals($expectedCount, $container->getContentsCount());
        }
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