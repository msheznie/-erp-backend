<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagSalesReturnDetails;

class POSStagSalesReturnDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_sales_return_details', $pOSStagSalesReturnDetails
        );

        $this->assertApiResponse($pOSStagSalesReturnDetails);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_sales_return_details/'.$pOSStagSalesReturnDetails->id
        );

        $this->assertApiResponse($pOSStagSalesReturnDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->create();
        $editedPOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_sales_return_details/'.$pOSStagSalesReturnDetails->id,
            $editedPOSStagSalesReturnDetails
        );

        $this->assertApiResponse($editedPOSStagSalesReturnDetails);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_sales_return_details/'.$pOSStagSalesReturnDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_sales_return_details/'.$pOSStagSalesReturnDetails->id
        );

        $this->response->assertStatus(404);
    }
}
