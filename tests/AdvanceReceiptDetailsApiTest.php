<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AdvanceReceiptDetails;

class AdvanceReceiptDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/advance_receipt_details', $advanceReceiptDetails
        );

        $this->assertApiResponse($advanceReceiptDetails);
    }

    /**
     * @test
     */
    public function test_read_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/advance_receipt_details/'.$advanceReceiptDetails->id
        );

        $this->assertApiResponse($advanceReceiptDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->create();
        $editedAdvanceReceiptDetails = factory(AdvanceReceiptDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/advance_receipt_details/'.$advanceReceiptDetails->id,
            $editedAdvanceReceiptDetails
        );

        $this->assertApiResponse($editedAdvanceReceiptDetails);
    }

    /**
     * @test
     */
    public function test_delete_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/advance_receipt_details/'.$advanceReceiptDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/advance_receipt_details/'.$advanceReceiptDetails->id
        );

        $this->response->assertStatus(404);
    }
}
