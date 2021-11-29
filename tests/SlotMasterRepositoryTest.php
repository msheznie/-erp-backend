<?php namespace Tests\Repositories;

use App\Models\SlotMaster;
use App\Repositories\SlotMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SlotMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SlotMasterRepository
     */
    protected $slotMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->slotMasterRepo = \App::make(SlotMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->make()->toArray();

        $createdSlotMaster = $this->slotMasterRepo->create($slotMaster);

        $createdSlotMaster = $createdSlotMaster->toArray();
        $this->assertArrayHasKey('id', $createdSlotMaster);
        $this->assertNotNull($createdSlotMaster['id'], 'Created SlotMaster must have id specified');
        $this->assertNotNull(SlotMaster::find($createdSlotMaster['id']), 'SlotMaster with given id must be in DB');
        $this->assertModelData($slotMaster, $createdSlotMaster);
    }

    /**
     * @test read
     */
    public function test_read_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->create();

        $dbSlotMaster = $this->slotMasterRepo->find($slotMaster->id);

        $dbSlotMaster = $dbSlotMaster->toArray();
        $this->assertModelData($slotMaster->toArray(), $dbSlotMaster);
    }

    /**
     * @test update
     */
    public function test_update_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->create();
        $fakeSlotMaster = factory(SlotMaster::class)->make()->toArray();

        $updatedSlotMaster = $this->slotMasterRepo->update($fakeSlotMaster, $slotMaster->id);

        $this->assertModelData($fakeSlotMaster, $updatedSlotMaster->toArray());
        $dbSlotMaster = $this->slotMasterRepo->find($slotMaster->id);
        $this->assertModelData($fakeSlotMaster, $dbSlotMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_slot_master()
    {
        $slotMaster = factory(SlotMaster::class)->create();

        $resp = $this->slotMasterRepo->delete($slotMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SlotMaster::find($slotMaster->id), 'SlotMaster should not exist in DB');
    }
}
