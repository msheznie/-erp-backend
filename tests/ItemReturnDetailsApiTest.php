<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnDetailsApiTest extends TestCase
{
    use MakeItemReturnDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemReturnDetails()
    {
        $itemReturnDetails = $this->fakeItemReturnDetailsData();
        $this->json('POST', '/api/v1/itemReturnDetails', $itemReturnDetails);

        $this->assertApiResponse($itemReturnDetails);
    }

    /**
     * @test
     */
    public function testReadItemReturnDetails()
    {
        $itemReturnDetails = $this->makeItemReturnDetails();
        $this->json('GET', '/api/v1/itemReturnDetails/'.$itemReturnDetails->id);

        $this->assertApiResponse($itemReturnDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemReturnDetails()
    {
        $itemReturnDetails = $this->makeItemReturnDetails();
        $editedItemReturnDetails = $this->fakeItemReturnDetailsData();

        $this->json('PUT', '/api/v1/itemReturnDetails/'.$itemReturnDetails->id, $editedItemReturnDetails);

        $this->assertApiResponse($editedItemReturnDetails);
    }

    /**
     * @test
     */
    public function testDeleteItemReturnDetails()
    {
        $itemReturnDetails = $this->makeItemReturnDetails();
        $this->json('DELETE', '/api/v1/itemReturnDetails/'.$itemReturnDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemReturnDetails/'.$itemReturnDetails->id);

        $this->assertResponseStatus(404);
    }
}
