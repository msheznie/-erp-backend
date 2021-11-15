<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SlotDetails;

class SlotDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/slot_details', $slotDetails
        );

        $this->assertApiResponse($slotDetails);
    }

    /**
     * @test
     */
    public function test_read_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/slot_details/'.$slotDetails->id
        );

        $this->assertApiResponse($slotDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->create();
        $editedSlotDetails = factory(SlotDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/slot_details/'.$slotDetails->id,
            $editedSlotDetails
        );

        $this->assertApiResponse($editedSlotDetails);
    }

    /**
     * @test
     */
    public function test_delete_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/slot_details/'.$slotDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/slot_details/'.$slotDetails->id
        );

        $this->response->assertStatus(404);
    }
}
