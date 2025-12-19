<?php namespace Tests\Repositories;

use App\Models\SlotMasterWeekDays;
use App\Repositories\SlotMasterWeekDaysRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SlotMasterWeekDaysRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SlotMasterWeekDaysRepository
     */
    protected $slotMasterWeekDaysRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->slotMasterWeekDaysRepo = \App::make(SlotMasterWeekDaysRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->make()->toArray();

        $createdSlotMasterWeekDays = $this->slotMasterWeekDaysRepo->create($slotMasterWeekDays);

        $createdSlotMasterWeekDays = $createdSlotMasterWeekDays->toArray();
        $this->assertArrayHasKey('id', $createdSlotMasterWeekDays);
        $this->assertNotNull($createdSlotMasterWeekDays['id'], 'Created SlotMasterWeekDays must have id specified');
        $this->assertNotNull(SlotMasterWeekDays::find($createdSlotMasterWeekDays['id']), 'SlotMasterWeekDays with given id must be in DB');
        $this->assertModelData($slotMasterWeekDays, $createdSlotMasterWeekDays);
    }

    /**
     * @test read
     */
    public function test_read_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->create();

        $dbSlotMasterWeekDays = $this->slotMasterWeekDaysRepo->find($slotMasterWeekDays->id);

        $dbSlotMasterWeekDays = $dbSlotMasterWeekDays->toArray();
        $this->assertModelData($slotMasterWeekDays->toArray(), $dbSlotMasterWeekDays);
    }

    /**
     * @test update
     */
    public function test_update_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->create();
        $fakeSlotMasterWeekDays = factory(SlotMasterWeekDays::class)->make()->toArray();

        $updatedSlotMasterWeekDays = $this->slotMasterWeekDaysRepo->update($fakeSlotMasterWeekDays, $slotMasterWeekDays->id);

        $this->assertModelData($fakeSlotMasterWeekDays, $updatedSlotMasterWeekDays->toArray());
        $dbSlotMasterWeekDays = $this->slotMasterWeekDaysRepo->find($slotMasterWeekDays->id);
        $this->assertModelData($fakeSlotMasterWeekDays, $dbSlotMasterWeekDays->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_slot_master_week_days()
    {
        $slotMasterWeekDays = factory(SlotMasterWeekDays::class)->create();

        $resp = $this->slotMasterWeekDaysRepo->delete($slotMasterWeekDays->id);

        $this->assertTrue($resp);
        $this->assertNull(SlotMasterWeekDays::find($slotMasterWeekDays->id), 'SlotMasterWeekDays should not exist in DB');
    }
}
