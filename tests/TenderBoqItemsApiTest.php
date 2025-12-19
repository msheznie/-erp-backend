<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderBoqItems;

class TenderBoqItemsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_boq_items', $tenderBoqItems
        );

        $this->assertApiResponse($tenderBoqItems);
    }

    /**
     * @test
     */
    public function test_read_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_boq_items/'.$tenderBoqItems->id
        );

        $this->assertApiResponse($tenderBoqItems->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->create();
        $editedTenderBoqItems = factory(TenderBoqItems::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_boq_items/'.$tenderBoqItems->id,
            $editedTenderBoqItems
        );

        $this->assertApiResponse($editedTenderBoqItems);
    }

    /**
     * @test
     */
    public function test_delete_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_boq_items/'.$tenderBoqItems->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_boq_items/'.$tenderBoqItems->id
        );

        $this->response->assertStatus(404);
    }
}
