<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentDetailsApiTest extends TestCase
{
    use MakeStockAdjustmentDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->fakeStockAdjustmentDetailsData();
        $this->json('POST', '/api/v1/stockAdjustmentDetails', $stockAdjustmentDetails);

        $this->assertApiResponse($stockAdjustmentDetails);
    }

    /**
     * @test
     */
    public function testReadStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->makeStockAdjustmentDetails();
        $this->json('GET', '/api/v1/stockAdjustmentDetails/'.$stockAdjustmentDetails->id);

        $this->assertApiResponse($stockAdjustmentDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->makeStockAdjustmentDetails();
        $editedStockAdjustmentDetails = $this->fakeStockAdjustmentDetailsData();

        $this->json('PUT', '/api/v1/stockAdjustmentDetails/'.$stockAdjustmentDetails->id, $editedStockAdjustmentDetails);

        $this->assertApiResponse($editedStockAdjustmentDetails);
    }

    /**
     * @test
     */
    public function testDeleteStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->makeStockAdjustmentDetails();
        $this->json('DELETE', '/api/v1/stockAdjustmentDetails/'.$stockAdjustmentDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockAdjustmentDetails/'.$stockAdjustmentDetails->id);

        $this->assertResponseStatus(404);
    }
}
