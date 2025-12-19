<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SlotMasterWeekDays;

class SlotMasterWeekDaysApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/slot_master_week_days', $slotMasterWeekDays
        );

        $this->assertApiResponse($slotMasterWeekDays);
    }

    /**
     * @test
     */
    public function test_read_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/slot_master_week_days/'.$slotMasterWeekDays->id
        );

        $this->assertApiResponse($slotMasterWeekDays->toArray());
    }

    /**
     * @test
     */
    public function test_update_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->create();
        $editedSlotMasterWeekDays = factory(SlotMasterWeekDays::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/slot_master_week_days/'.$slotMasterWeekDays->id,
            $editedSlotMasterWeekDays
        );

        $this->assertApiResponse($editedSlotMasterWeekDays);
    }

    /**
     * @test
     */
    public function test_delete_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/slot_master_week_days/'.$slotMasterWeekDays->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/slot_master_week_days/'.$slotMasterWeekDays->id
        );

        $this->response->assertStatus(404);
    }
}
