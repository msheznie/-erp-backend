<?php namespace Tests\Repositories;

use App\Models\SlotDetails;
use App\Repositories\SlotDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SlotDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SlotDetailsRepository
     */
    protected $slotDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->slotDetailsRepo = \App::make(SlotDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->make()->toArray();

        $createdSlotDetails = $this->slotDetailsRepo->create($slotDetails);

        $createdSlotDetails = $createdSlotDetails->toArray();
        $this->assertArrayHasKey('id', $createdSlotDetails);
        $this->assertNotNull($createdSlotDetails['id'], 'Created SlotDetails must have id specified');
        $this->assertNotNull(SlotDetails::find($createdSlotDetails['id']), 'SlotDetails with given id must be in DB');
        $this->assertModelData($slotDetails, $createdSlotDetails);
    }

    /**
     * @test read
     */
    public function test_read_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->create();

        $dbSlotDetails = $this->slotDetailsRepo->find($slotDetails->id);

        $dbSlotDetails = $dbSlotDetails->toArray();
        $this->assertModelData($slotDetails->toArray(), $dbSlotDetails);
    }

    /**
     * @test update
     */
    public function test_update_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->create();
        $fakeSlotDetails = factory(SlotDetails::class)->make()->toArray();

        $updatedSlotDetails = $this->slotDetailsRepo->update($fakeSlotDetails, $slotDetails->id);

        $this->assertModelData($fakeSlotDetails, $updatedSlotDetails->toArray());
        $dbSlotDetails = $this->slotDetailsRepo->find($slotDetails->id);
        $this->assertModelData($fakeSlotDetails, $dbSlotDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_slot_details()
    {
        $slotDetails = factory(SlotDetails::class)->create();

        $resp = $this->slotDetailsRepo->delete($slotDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(SlotDetails::find($slotDetails->id), 'SlotDetails should not exist in DB');
    }
}
