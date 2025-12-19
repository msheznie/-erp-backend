<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SlotMaster;

class SlotMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/slot_masters', $slotMaster
        );

        $this->assertApiResponse($slotMaster);
    }

    /**
     * @test
     */
    public function test_read_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/slot_masters/'.$slotMaster->id
        );

        $this->assertApiResponse($slotMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->create();
        $editedSlotMaster = factory(SlotMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/slot_masters/'.$slotMaster->id,
            $editedSlotMaster
        );

        $this->assertApiResponse($editedSlotMaster);
    }

    /**
     * @test
     */
    public function test_delete_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/slot_masters/'.$slotMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/slot_masters/'.$slotMaster->id
        );

        $this->response->assertStatus(404);
    }
}
