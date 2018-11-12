<?php

use App\Models\HRMSDepartmentMaster;
use App\Repositories\HRMSDepartmentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSDepartmentMasterRepositoryTest extends TestCase
{
    use MakeHRMSDepartmentMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSDepartmentMasterRepository
     */
    protected $hRMSDepartmentMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->hRMSDepartmentMasterRepo = App::make(HRMSDepartmentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->fakeHRMSDepartmentMasterData();
        $createdHRMSDepartmentMaster = $this->hRMSDepartmentMasterRepo->create($hRMSDepartmentMaster);
        $createdHRMSDepartmentMaster = $createdHRMSDepartmentMaster->toArray();
        $this->assertArrayHasKey('id', $createdHRMSDepartmentMaster);
        $this->assertNotNull($createdHRMSDepartmentMaster['id'], 'Created HRMSDepartmentMaster must have id specified');
        $this->assertNotNull(HRMSDepartmentMaster::find($createdHRMSDepartmentMaster['id']), 'HRMSDepartmentMaster with given id must be in DB');
        $this->assertModelData($hRMSDepartmentMaster, $createdHRMSDepartmentMaster);
    }

    /**
     * @test read
     */
    public function testReadHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->makeHRMSDepartmentMaster();
        $dbHRMSDepartmentMaster = $this->hRMSDepartmentMasterRepo->find($hRMSDepartmentMaster->id);
        $dbHRMSDepartmentMaster = $dbHRMSDepartmentMaster->toArray();
        $this->assertModelData($hRMSDepartmentMaster->toArray(), $dbHRMSDepartmentMaster);
    }

    /**
     * @test update
     */
    public function testUpdateHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->makeHRMSDepartmentMaster();
        $fakeHRMSDepartmentMaster = $this->fakeHRMSDepartmentMasterData();
        $updatedHRMSDepartmentMaster = $this->hRMSDepartmentMasterRepo->update($fakeHRMSDepartmentMaster, $hRMSDepartmentMaster->id);
        $this->assertModelData($fakeHRMSDepartmentMaster, $updatedHRMSDepartmentMaster->toArray());
        $dbHRMSDepartmentMaster = $this->hRMSDepartmentMasterRepo->find($hRMSDepartmentMaster->id);
        $this->assertModelData($fakeHRMSDepartmentMaster, $dbHRMSDepartmentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->makeHRMSDepartmentMaster();
        $resp = $this->hRMSDepartmentMasterRepo->delete($hRMSDepartmentMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSDepartmentMaster::find($hRMSDepartmentMaster->id), 'HRMSDepartmentMaster should not exist in DB');
    }
}
