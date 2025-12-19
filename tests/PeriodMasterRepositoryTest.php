<?php

use App\Models\PeriodMaster;
use App\Repositories\PeriodMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PeriodMasterRepositoryTest extends TestCase
{
    use MakePeriodMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PeriodMasterRepository
     */
    protected $periodMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->periodMasterRepo = App::make(PeriodMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePeriodMaster()
    {
        $periodMaster = $this->fakePeriodMasterData();
        $createdPeriodMaster = $this->periodMasterRepo->create($periodMaster);
        $createdPeriodMaster = $createdPeriodMaster->toArray();
        $this->assertArrayHasKey('id', $createdPeriodMaster);
        $this->assertNotNull($createdPeriodMaster['id'], 'Created PeriodMaster must have id specified');
        $this->assertNotNull(PeriodMaster::find($createdPeriodMaster['id']), 'PeriodMaster with given id must be in DB');
        $this->assertModelData($periodMaster, $createdPeriodMaster);
    }

    /**
     * @test read
     */
    public function testReadPeriodMaster()
    {
        $periodMaster = $this->makePeriodMaster();
        $dbPeriodMaster = $this->periodMasterRepo->find($periodMaster->id);
        $dbPeriodMaster = $dbPeriodMaster->toArray();
        $this->assertModelData($periodMaster->toArray(), $dbPeriodMaster);
    }

    /**
     * @test update
     */
    public function testUpdatePeriodMaster()
    {
        $periodMaster = $this->makePeriodMaster();
        $fakePeriodMaster = $this->fakePeriodMasterData();
        $updatedPeriodMaster = $this->periodMasterRepo->update($fakePeriodMaster, $periodMaster->id);
        $this->assertModelData($fakePeriodMaster, $updatedPeriodMaster->toArray());
        $dbPeriodMaster = $this->periodMasterRepo->find($periodMaster->id);
        $this->assertModelData($fakePeriodMaster, $dbPeriodMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePeriodMaster()
    {
        $periodMaster = $this->makePeriodMaster();
        $resp = $this->periodMasterRepo->delete($periodMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(PeriodMaster::find($periodMaster->id), 'PeriodMaster should not exist in DB');
    }
}
