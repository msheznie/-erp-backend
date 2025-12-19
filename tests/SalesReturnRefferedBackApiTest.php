<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SalesReturnRefferedBack;

class SalesReturnRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales_return_reffered_backs', $salesReturnRefferedBack
        );

        $this->assertApiResponse($salesReturnRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/sales_return_reffered_backs/'.$salesReturnRefferedBack->id
        );

        $this->assertApiResponse($salesReturnRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->create();
        $editedSalesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales_return_reffered_backs/'.$salesReturnRefferedBack->id,
            $editedSalesReturnRefferedBack
        );

        $this->assertApiResponse($editedSalesReturnRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales_return_reffered_backs/'.$salesReturnRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales_return_reffered_backs/'.$salesReturnRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}
