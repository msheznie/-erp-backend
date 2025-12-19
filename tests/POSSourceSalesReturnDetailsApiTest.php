<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceSalesReturnDetails;

class POSSourceSalesReturnDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_sales_return_details', $pOSSourceSalesReturnDetails
        );

        $this->assertApiResponse($pOSSourceSalesReturnDetails);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_sales_return_details/'.$pOSSourceSalesReturnDetails->id
        );

        $this->assertApiResponse($pOSSourceSalesReturnDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->create();
        $editedPOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_sales_return_details/'.$pOSSourceSalesReturnDetails->id,
            $editedPOSSourceSalesReturnDetails
        );

        $this->assertApiResponse($editedPOSSourceSalesReturnDetails);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_sales_return_details/'.$pOSSourceSalesReturnDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_sales_return_details/'.$pOSSourceSalesReturnDetails->id
        );

        $this->response->assertStatus(404);
    }
}
