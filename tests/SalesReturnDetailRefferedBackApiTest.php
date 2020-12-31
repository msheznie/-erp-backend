<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SalesReturnDetailRefferedBack;

class SalesReturnDetailRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales_return_detail_reffered_backs', $salesReturnDetailRefferedBack
        );

        $this->assertApiResponse($salesReturnDetailRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/sales_return_detail_reffered_backs/'.$salesReturnDetailRefferedBack->id
        );

        $this->assertApiResponse($salesReturnDetailRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->create();
        $editedSalesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales_return_detail_reffered_backs/'.$salesReturnDetailRefferedBack->id,
            $editedSalesReturnDetailRefferedBack
        );

        $this->assertApiResponse($editedSalesReturnDetailRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales_return_detail_reffered_backs/'.$salesReturnDetailRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales_return_detail_reffered_backs/'.$salesReturnDetailRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}
