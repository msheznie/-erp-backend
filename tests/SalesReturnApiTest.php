<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SalesReturn;

class SalesReturnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales_returns', $salesReturn
        );

        $this->assertApiResponse($salesReturn);
    }

    /**
     * @test
     */
    public function test_read_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/sales_returns/'.$salesReturn->id
        );

        $this->assertApiResponse($salesReturn->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->create();
        $editedSalesReturn = factory(SalesReturn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales_returns/'.$salesReturn->id,
            $editedSalesReturn
        );

        $this->assertApiResponse($editedSalesReturn);
    }

    /**
     * @test
     */
    public function test_delete_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales_returns/'.$salesReturn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales_returns/'.$salesReturn->id
        );

        $this->response->assertStatus(404);
    }
}
