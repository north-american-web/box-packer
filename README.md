# Yet Another Box Packer Package

This module implements a rough solution to the [bin packing problem](https://en.wikipedia.org/wiki/Bin_packing_problem).
It was originally developed to support an e-commerce merchant's desire to offer a shipping option only if all the items
in an order would fit into a certain box. This implementation supports multiple boxes as well.

The bin-packing problem is [NP hard](https://en.wikipedia.org/wiki/NP-hardness). This implementation is a heuristic 
that won't always provide an optimal solution, but it is fast and it's unlikely to provide a solution that's too
difficult for a human to replicate when packing a box with real (physical) items.

Briefly, this algorithm works as follows:

* Largest (by volume) items are packed first.
* Items are packed in layers. Layers are as wide and long as the box and are as tall as the tallest object in the layer.
* Items are packed alongside each other in each layer with the longest edge on the bottom, oriented in a way 
that yields the largest remaining area.
* If an item doesn't fit in any existing layers, the algorithm starts a new layer.
* Each time an item is added, the algorithm attempts to add it to the lower layers in any remaining spaces (including 
above other items), unless doing so would increase the height of the layer.
* Each time a new layer is started, the algorithm checks to make sure the total height of the layers hasn't exceeded the 
box height.

##Using NAWebCo\BoxPacker
To install, run ```composer require nawebco/box-packer``` in your project's root directory. If you aren't already using
Composer's autoloader, be sure to require `vendor/autoload.php` wherever you need access to the BoxPacker.

Usage example:
```$xslt
<?php
require 'vendor/autoload.php';

use NAWebCo\BoxPacker\Packer;
use NAWebCo\BoxPacker\Container;
use NAWebCo\BoxPacker\Solid;


$packer = new Packer();

// Width, length, height, description
// The order of the dimensions doesn't matter.
$packer->addContainer(new Container(10, 15, 6, 'Big box'));

// Width, length, height, description
// The order of the dimensions doesn't matter.
$packer->addItem(new Solid(8.5, 11, 2, 'First book'));
$packer->addItem(new Solid(8.5, 11, 1.2, 'Second book'));
$packer->addItem(new Solid(3, 3, 3, 'Carved wooden block'));

$result = $packer->pack();

$packedBoxes = $result->getPackedContainers();

if( $result->success() ){
    echo "Everything fits!\n\n";

    foreach( $result->getPackedContainers() as $box ){
        $containerDescription = $box->getDescription() ?: 'One box';
        echo "$containerDescription contains:\n";

        foreach( $box->getPackedSolids() as $item ){
            $itemDescription = $item->getDescription() ?: 'Item';
            echo "$itemDescription ({$item->getWidth()}, {$item->getLength()}, {$item->getHeight()})\n";
        }
    }
} else {
    echo count($result->getNotPackedItems()) . " items didn't fit.";
}
```

In the example above, packable items are represented by the `Solid` class. Any class that implements the `SolidInterface`
can be run through the Packer.

##Requirements
* PHP version 5.6 or higher

##License
NAWebCo\BoxPacker is MIT-licensed.