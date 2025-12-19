<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\QuotationStatusMaster;

class QuotationStatusMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/quotation_status_masters', $quotationStatusMaster
        );

        $this->assertApiResponse($quotationStatusMaster);
    }

    /**
     * @test
     */
    public function test_read_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/quotation_status_masters/'.$quotationStatusMaster->id
        );

        $this->assertApiResponse($quotationStatusMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->create();
        $editedQuotationStatusMaster = factory(QuotationStatusMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/quotation_status_masters/'.$quotationStatusMaster->id,
            $editedQuotationStatusMaster
        );

        $this->assertApiResponse($editedQuotationStatusMaster);
    }

    /**
     * @test
     */
    public function test_delete_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/quotation_status_masters/'.$quotationStatusMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/quotation_status_masters/'.$quotationStatusMaster->id
        );

        $this->response->assertStatus(404);
    }
}
