<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SalesReturnDetail;

class SalesReturnDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales_return_details', $salesReturnDetail
        );

        $this->assertApiResponse($salesReturnDetail);
    }

    /**
     * @test
     */
    public function test_read_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/sales_return_details/'.$salesReturnDetail->id
        );

        $this->assertApiResponse($salesReturnDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->create();
        $editedSalesReturnDetail = factory(SalesReturnDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales_return_details/'.$salesReturnDetail->id,
            $editedSalesReturnDetail
        );

        $this->assertApiResponse($editedSalesReturnDetail);
    }

    /**
     * @test
     */
    public function test_delete_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales_return_details/'.$salesReturnDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales_return_details/'.$salesReturnDetail->id
        );

        $this->response->assertStatus(404);
    }
}
