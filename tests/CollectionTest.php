<?php

use \Toolkit\Collection\ConcreteCollection;

require __DIR__ . '/../vendor/autoload.php';

class CollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Collection
     */
    private $coll;

    public function setUp()
    {
        $this->coll = new ConcreteCollection();
    }

    public function test_add_item_with_key_item_exists()
    {
        $this->add_multiple(1);
        $this->assertTrue($this->coll->exists('key1'));
    }

    public function test_add_item_no_key_item_exists()
    {
        $this->coll->add('value1');
        $this->assertTrue($this->coll->exists(0));
    }

    /**
     * @expectedException \Toolkit\Exceptions\InvalidKeyException
     * @expectedExceptionMessage Key already exists
     */
    public function test_invalid_key_exception_already_exists()
    {
        $this->coll->add('value1', 'key1');
        $this->coll->add('value1', 'key1');
    }

    public function test_add_multiple_items()
    {
        $this->add_multiple(2);
        $this->assertEquals(2, $this->coll->length());
    }

    public function test_retrieve_value_by_key()
    {
        $this->add_multiple(1);
        $this->assertEquals('value1', $this->coll->item('key1'));
    }

    public function test_retrieve_value_by_index()
    {
        $this->coll->add('value1');
        $this->assertEquals('value1', $this->coll->item(0));
    }

    /**
     * @expectedException \Toolkit\Exceptions\InvalidKeyException
     * @expectedExceptionMessage Key does not exist
     */
    public function test_retrieve_value_no_key_exception()
    {
        $this->coll->item('key1');
    }


    public function test_remove_one_item()
    {
        $this->add_multiple(4);
        $this->coll->remove('key1');
        $this->assertEquals(3, $this->coll->length());
    }

    /**
     * @expectedException \Toolkit\Exceptions\InvalidKeyException
     * @expectedExceptionMessage Key does not exist
     */
    public function test_remove_item_no_key_exception()
    {
        $this->coll->remove('key1');
    }

    public function test_retrieve_key()
    {
        $this->add_multiple(1);
        $this->assertEquals(['key1'], $this->coll->keys());
    }

    public function test_retrieve_multiple_keys()
    {
        $this->add_multiple(5);
        $expected = ['key1', 'key2', 'key3', 'key4', 'key5'];
        $this->assertEquals($expected, $this->coll->keys());
    }

    public function test_iterate_through_collection()
    {
        $this->add_multiple(2);
        $expected = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];
        foreach ($this->coll as $key => $item) {
            $actual[$key] = $item;
        }
        $this->assertEquals($expected, $actual);
    }

    public function test_array_access_add_and_retrieve_by_key()
    {
        $this->coll['key1'] = 'value1';
        $this->assertEquals("value1", $this->coll['key1']);
    }

    public function test_array_access_add_and_retrieve_by_index()
    {
        $this->coll[] = 'value1';
        $this->assertEquals('value1', $this->coll[0]);
    }

    /**
     * @expectedException \Toolkit\Exceptions\InvalidKeyException
     * @expectedExceptionMessage Key does not exist
     */
    public function test_array_access_no_key_exists_exception()
    {
        $value = $this->coll[0];
    }

    public function test_array_access_unset_element()
    {
        $this->add_multiple(2);
        unset($this->coll['key1']);
        $this->assertEquals(1, $this->coll->length());
    }

    private function add_multiple(int $items)
    {
        for ($i = 1; $i <= $items; $i++) {
            $this->coll->add("value{$i}", "key{$i}");
        }
    }
}