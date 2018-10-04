<?php

use App\Models\HRMSJvMaster;
use App\Repositories\HRMSJvMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSJvMasterRepositoryTest extends TestCase
{
    use MakeHRMSJvMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSJvMasterRepository
     */
    protected $hRMSJvMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->hRMSJvMasterRepo = App::make(HRMSJvMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateHRMSJvMaster()
    {
        $hRMSJvMaster = $this->fakeHRMSJvMasterData();
        $createdHRMSJvMaster = $this->hRMSJvMasterRepo->create($hRMSJvMaster);
        $createdHRMSJvMaster = $createdHRMSJvMaster->toArray();
        $this->assertArrayHasKey('id', $createdHRMSJvMaster);
        $this->assertNotNull($createdHRMSJvMaster['id'], 'Created HRMSJvMaster must have id specified');
        $this->assertNotNull(HRMSJvMaster::find($createdHRMSJvMaster['id']), 'HRMSJvMaster with given id must be in DB');
        $this->assertModelData($hRMSJvMaster, $createdHRMSJvMaster);
    }

    /**
     * @test read
     */
    public function testReadHRMSJvMaster()
    {
        $hRMSJvMaster = $this->makeHRMSJvMaster();
        $dbHRMSJvMaster = $this->hRMSJvMasterRepo->find($hRMSJvMaster->id);
        $dbHRMSJvMaster = $dbHRMSJvMaster->toArray();
        $this->assertModelData($hRMSJvMaster->toArray(), $dbHRMSJvMaster);
    }

    /**
     * @test update
     */
    public function testUpdateHRMSJvMaster()
    {
        $hRMSJvMaster = $this->makeHRMSJvMaster();
        $fakeHRMSJvMaster = $this->fakeHRMSJvMasterData();
        $updatedHRMSJvMaster = $this->hRMSJvMasterRepo->update($fakeHRMSJvMaster, $hRMSJvMaster->id);
        $this->assertModelData($fakeHRMSJvMaster, $updatedHRMSJvMaster->toArray());
        $dbHRMSJvMaster = $this->hRMSJvMasterRepo->find($hRMSJvMaster->id);
        $this->assertModelData($fakeHRMSJvMaster, $dbHRMSJvMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteHRMSJvMaster()
    {
        $hRMSJvMaster = $this->makeHRMSJvMaster();
        $resp = $this->hRMSJvMasterRepo->delete($hRMSJvMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSJvMaster::find($hRMSJvMaster->id), 'HRMSJvMaster should not exist in DB');
    }
}
