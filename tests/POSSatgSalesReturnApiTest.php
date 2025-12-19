<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSatgSalesReturn;

class POSSatgSalesReturnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_satg_sales_returns', $pOSSatgSalesReturn
        );

        $this->assertApiResponse($pOSSatgSalesReturn);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_satg_sales_returns/'.$pOSSatgSalesReturn->id
        );

        $this->assertApiResponse($pOSSatgSalesReturn->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->create();
        $editedPOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_satg_sales_returns/'.$pOSSatgSalesReturn->id,
            $editedPOSSatgSalesReturn
        );

        $this->assertApiResponse($editedPOSSatgSalesReturn);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_satg_sales_returns/'.$pOSSatgSalesReturn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_satg_sales_returns/'.$pOSSatgSalesReturn->id
        );

        $this->response->assertStatus(404);
    }
}
