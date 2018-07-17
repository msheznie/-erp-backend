<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferDetailsApiTest extends TestCase
{
    use MakeStockTransferDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockTransferDetails()
    {
        $stockTransferDetails = $this->fakeStockTransferDetailsData();
        $this->json('POST', '/api/v1/stockTransferDetails', $stockTransferDetails);

        $this->assertApiResponse($stockTransferDetails);
    }

    /**
     * @test
     */
    public function testReadStockTransferDetails()
    {
        $stockTransferDetails = $this->makeStockTransferDetails();
        $this->json('GET', '/api/v1/stockTransferDetails/'.$stockTransferDetails->id);

        $this->assertApiResponse($stockTransferDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockTransferDetails()
    {
        $stockTransferDetails = $this->makeStockTransferDetails();
        $editedStockTransferDetails = $this->fakeStockTransferDetailsData();

        $this->json('PUT', '/api/v1/stockTransferDetails/'.$stockTransferDetails->id, $editedStockTransferDetails);

        $this->assertApiResponse($editedStockTransferDetails);
    }

    /**
     * @test
     */
    public function testDeleteStockTransferDetails()
    {
        $stockTransferDetails = $this->makeStockTransferDetails();
        $this->json('DELETE', '/api/v1/stockTransferDetails/'.$stockTransferDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockTransferDetails/'.$stockTransferDetails->id);

        $this->assertResponseStatus(404);
    }
}
