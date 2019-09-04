<?php namespace Tests\Repositories;

use App\Models\ScheduleMaster;
use App\Repositories\ScheduleMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeScheduleMasterTrait;
use Tests\ApiTestTrait;

class ScheduleMasterRepositoryTest extends TestCase
{
    use MakeScheduleMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ScheduleMasterRepository
     */
    protected $scheduleMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->scheduleMasterRepo = \App::make(ScheduleMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_schedule_master()
    {
        $scheduleMaster = $this->fakeScheduleMasterData();
        $createdScheduleMaster = $this->scheduleMasterRepo->create($scheduleMaster);
        $createdScheduleMaster = $createdScheduleMaster->toArray();
        $this->assertArrayHasKey('id', $createdScheduleMaster);
        $this->assertNotNull($createdScheduleMaster['id'], 'Created ScheduleMaster must have id specified');
        $this->assertNotNull(ScheduleMaster::find($createdScheduleMaster['id']), 'ScheduleMaster with given id must be in DB');
        $this->assertModelData($scheduleMaster, $createdScheduleMaster);
    }

    /**
     * @test read
     */
    public function test_read_schedule_master()
    {
        $scheduleMaster = $this->makeScheduleMaster();
        $dbScheduleMaster = $this->scheduleMasterRepo->find($scheduleMaster->id);
        $dbScheduleMaster = $dbScheduleMaster->toArray();
        $this->assertModelData($scheduleMaster->toArray(), $dbScheduleMaster);
    }

    /**
     * @test update
     */
    public function test_update_schedule_master()
    {
        $scheduleMaster = $this->makeScheduleMaster();
        $fakeScheduleMaster = $this->fakeScheduleMasterData();
        $updatedScheduleMaster = $this->scheduleMasterRepo->update($fakeScheduleMaster, $scheduleMaster->id);
        $this->assertModelData($fakeScheduleMaster, $updatedScheduleMaster->toArray());
        $dbScheduleMaster = $this->scheduleMasterRepo->find($scheduleMaster->id);
        $this->assertModelData($fakeScheduleMaster, $dbScheduleMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_schedule_master()
    {
        $scheduleMaster = $this->makeScheduleMaster();
        $resp = $this->scheduleMasterRepo->delete($scheduleMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ScheduleMaster::find($scheduleMaster->id), 'ScheduleMaster should not exist in DB');
    }
}
