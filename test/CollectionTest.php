<?php

use \Toolkit\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function AddItemWithoutKey()
    {
        $coll = new Collection();
        $coll->addItem("Collection item 1");
        $this->assertEquals("Collection item 1", $coll[0]);
    }
}
