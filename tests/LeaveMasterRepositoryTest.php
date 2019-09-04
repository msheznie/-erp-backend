<?php namespace Tests\Repositories;

use App\Models\LeaveMaster;
use App\Repositories\LeaveMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveMasterTrait;
use Tests\ApiTestTrait;

class LeaveMasterRepositoryTest extends TestCase
{
    use MakeLeaveMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveMasterRepository
     */
    protected $leaveMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveMasterRepo = \App::make(LeaveMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_master()
    {
        $leaveMaster = $this->fakeLeaveMasterData();
        $createdLeaveMaster = $this->leaveMasterRepo->create($leaveMaster);
        $createdLeaveMaster = $createdLeaveMaster->toArray();
        $this->assertArrayHasKey('id', $createdLeaveMaster);
        $this->assertNotNull($createdLeaveMaster['id'], 'Created LeaveMaster must have id specified');
        $this->assertNotNull(LeaveMaster::find($createdLeaveMaster['id']), 'LeaveMaster with given id must be in DB');
        $this->assertModelData($leaveMaster, $createdLeaveMaster);
    }

    /**
     * @test read
     */
    public function test_read_leave_master()
    {
        $leaveMaster = $this->makeLeaveMaster();
        $dbLeaveMaster = $this->leaveMasterRepo->find($leaveMaster->id);
        $dbLeaveMaster = $dbLeaveMaster->toArray();
        $this->assertModelData($leaveMaster->toArray(), $dbLeaveMaster);
    }

    /**
     * @test update
     */
    public function test_update_leave_master()
    {
        $leaveMaster = $this->makeLeaveMaster();
        $fakeLeaveMaster = $this->fakeLeaveMasterData();
        $updatedLeaveMaster = $this->leaveMasterRepo->update($fakeLeaveMaster, $leaveMaster->id);
        $this->assertModelData($fakeLeaveMaster, $updatedLeaveMaster->toArray());
        $dbLeaveMaster = $this->leaveMasterRepo->find($leaveMaster->id);
        $this->assertModelData($fakeLeaveMaster, $dbLeaveMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_master()
    {
        $leaveMaster = $this->makeLeaveMaster();
        $resp = $this->leaveMasterRepo->delete($leaveMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(LeaveMaster::find($leaveMaster->id), 'LeaveMaster should not exist in DB');
    }
}
