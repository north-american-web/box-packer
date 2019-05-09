<?php


namespace NAWebCo\BoxPackerTest;

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
        $containers = [
            new Container(1,1,1),
            new Container(2,2,2),
        ];
        foreach( $containers as $container ){
            /** @var Container $container */
            $container->addSolid(new Solid(1,1,1));
        }

        $containers[] = new Container(1,1,1);

        $result = new PackingResult($containers, []);

        $this->assertCount(2, $result->getPackedContainers());
        $this->assertCount(1, $result->getEmptyContainers());
    }


}