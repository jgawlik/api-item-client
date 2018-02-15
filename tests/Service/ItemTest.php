<?php

declare(strict_types=1);

namespace ApiClient\Tests\Service;

use ApiClient\Service\Item;
use ApiClient\Tests\ServiceTest;

class ItemTest extends ServiceTest
{
    /**
     * @test
     */
    public function itReturnsOneItem()
    {
        $item = new Item($this->createClientMock(200, 'get_item_response.json'));
        $response = $item->get(5);
        $this->assertEquals(5, $response['id']);
        $this->assertEquals(2, $response['amount']);
        $this->assertEquals('Product 8', $response['name']);
    }

    /**
     * @test
     * @expectedException \ApiClient\Exception\ItemClientException
     */
    public function itThrowsClientExceptionOnGet()
    {
        $item = new Item($this->createClientMock(404, 'get_item_response_exception.json'));
        $item->get(5121);
    }

    /**
     * @test
     */
    public function itReturnsItemCollection()
    {
        $item = new Item($this->createClientMock(200, 'get_items_by_params_1_response.json'));
        $response = $item->getByParams([]);
        $this->assertCount(5, $response);
    }

    /**
     * @test
     */
    public function itAddsNewItem()
    {
        $item = new Item($this->createClientMock(200, 'add_item_response.json'));
        $response = $item->add(['amount' => 15, 'name' => 'Test']);
        $this->assertEquals(6, $response['id']);
        $this->assertEquals(15, $response['amount']);
        $this->assertEquals('Test', $response['name']);
    }

    /**
     * @test
     * @expectedException \ApiClient\Exception\ItemClientException
     */
    public function itThrowsClientExceptionOnAddItem()
    {
        $item = new Item($this->createClientMock(422, 'add_update_item_client_exception.json'));
        $item->add(['name' => 'Test']);
    }

    /**
     * @test
     */
    public function itUpdateItem()
    {
        $item = new Item($this->createClientMock(200, 'empty_response.json'));
        $this->assertNull($item->update(['amount' => 11, 'name' => 'Test'], 4));
    }

    /**
     * @test
     * @expectedException \ApiClient\Exception\ItemClientException
     */
    public function itThrowsClientExceptionOnUpdateItem()
    {
        $item = new Item($this->createClientMock(422, 'add_update_item_client_exception.json'));
        $item->update(['name' => 'Test'], 4);
    }

    /**
     * @test
     */
    public function itRemovesItem()
    {
        $item = new Item($this->createClientMock(200, 'empty_response.json'));
        $this->assertNull($item->remove(1));
    }
}
