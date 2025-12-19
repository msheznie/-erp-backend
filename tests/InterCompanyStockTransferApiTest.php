<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\InterCompanyStockTransfer;

class InterCompanyStockTransferApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/inter_company_stock_transfers', $interCompanyStockTransfer
        );

        $this->assertApiResponse($interCompanyStockTransfer);
    }

    /**
     * @test
     */
    public function test_read_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/inter_company_stock_transfers/'.$interCompanyStockTransfer->id
        );

        $this->assertApiResponse($interCompanyStockTransfer->toArray());
    }

    /**
     * @test
     */
    public function test_update_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->create();
        $editedInterCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/inter_company_stock_transfers/'.$interCompanyStockTransfer->id,
            $editedInterCompanyStockTransfer
        );

        $this->assertApiResponse($editedInterCompanyStockTransfer);
    }

    /**
     * @test
     */
    public function test_delete_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/inter_company_stock_transfers/'.$interCompanyStockTransfer->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/inter_company_stock_transfers/'.$interCompanyStockTransfer->id
        );

        $this->response->assertStatus(404);
    }
}
