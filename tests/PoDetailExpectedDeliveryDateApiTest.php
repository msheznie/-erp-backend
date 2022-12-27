<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PoDetailExpectedDeliveryDate;

class PoDetailExpectedDeliveryDateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/po_detail_expected_delivery_dates', $poDetailExpectedDeliveryDate
        );

        $this->assertApiResponse($poDetailExpectedDeliveryDate);
    }

    /**
     * @test
     */
    public function test_read_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/po_detail_expected_delivery_dates/'.$poDetailExpectedDeliveryDate->id
        );

        $this->assertApiResponse($poDetailExpectedDeliveryDate->toArray());
    }

    /**
     * @test
     */
    public function test_update_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->create();
        $editedPoDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/po_detail_expected_delivery_dates/'.$poDetailExpectedDeliveryDate->id,
            $editedPoDetailExpectedDeliveryDate
        );

        $this->assertApiResponse($editedPoDetailExpectedDeliveryDate);
    }

    /**
     * @test
     */
    public function test_delete_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/po_detail_expected_delivery_dates/'.$poDetailExpectedDeliveryDate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/po_detail_expected_delivery_dates/'.$poDetailExpectedDeliveryDate->id
        );

        $this->response->assertStatus(404);
    }
}
