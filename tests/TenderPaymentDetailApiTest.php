<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderPaymentDetail;

class TenderPaymentDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_payment_details', $tenderPaymentDetail
        );

        $this->assertApiResponse($tenderPaymentDetail);
    }

    /**
     * @test
     */
    public function test_read_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_payment_details/'.$tenderPaymentDetail->id
        );

        $this->assertApiResponse($tenderPaymentDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->create();
        $editedTenderPaymentDetail = factory(TenderPaymentDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_payment_details/'.$tenderPaymentDetail->id,
            $editedTenderPaymentDetail
        );

        $this->assertApiResponse($editedTenderPaymentDetail);
    }

    /**
     * @test
     */
    public function test_delete_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_payment_details/'.$tenderPaymentDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_payment_details/'.$tenderPaymentDetail->id
        );

        $this->response->assertStatus(404);
    }
}
