<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\QuotationStatus;

class QuotationStatusApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/quotation_statuses', $quotationStatus
        );

        $this->assertApiResponse($quotationStatus);
    }

    /**
     * @test
     */
    public function test_read_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/quotation_statuses/'.$quotationStatus->id
        );

        $this->assertApiResponse($quotationStatus->toArray());
    }

    /**
     * @test
     */
    public function test_update_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->create();
        $editedQuotationStatus = factory(QuotationStatus::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/quotation_statuses/'.$quotationStatus->id,
            $editedQuotationStatus
        );

        $this->assertApiResponse($editedQuotationStatus);
    }

    /**
     * @test
     */
    public function test_delete_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/quotation_statuses/'.$quotationStatus->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/quotation_statuses/'.$quotationStatus->id
        );

        $this->response->assertStatus(404);
    }
}
