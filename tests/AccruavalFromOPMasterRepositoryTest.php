<?php

use App\Models\AccruavalFromOPMaster;
use App\Repositories\AccruavalFromOPMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccruavalFromOPMasterRepositoryTest extends TestCase
{
    use MakeAccruavalFromOPMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccruavalFromOPMasterRepository
     */
    protected $accruavalFromOPMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->accruavalFromOPMasterRepo = App::make(AccruavalFromOPMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->fakeAccruavalFromOPMasterData();
        $createdAccruavalFromOPMaster = $this->accruavalFromOPMasterRepo->create($accruavalFromOPMaster);
        $createdAccruavalFromOPMaster = $createdAccruavalFromOPMaster->toArray();
        $this->assertArrayHasKey('id', $createdAccruavalFromOPMaster);
        $this->assertNotNull($createdAccruavalFromOPMaster['id'], 'Created AccruavalFromOPMaster must have id specified');
        $this->assertNotNull(AccruavalFromOPMaster::find($createdAccruavalFromOPMaster['id']), 'AccruavalFromOPMaster with given id must be in DB');
        $this->assertModelData($accruavalFromOPMaster, $createdAccruavalFromOPMaster);
    }

    /**
     * @test read
     */
    public function testReadAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->makeAccruavalFromOPMaster();
        $dbAccruavalFromOPMaster = $this->accruavalFromOPMasterRepo->find($accruavalFromOPMaster->id);
        $dbAccruavalFromOPMaster = $dbAccruavalFromOPMaster->toArray();
        $this->assertModelData($accruavalFromOPMaster->toArray(), $dbAccruavalFromOPMaster);
    }

    /**
     * @test update
     */
    public function testUpdateAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->makeAccruavalFromOPMaster();
        $fakeAccruavalFromOPMaster = $this->fakeAccruavalFromOPMasterData();
        $updatedAccruavalFromOPMaster = $this->accruavalFromOPMasterRepo->update($fakeAccruavalFromOPMaster, $accruavalFromOPMaster->id);
        $this->assertModelData($fakeAccruavalFromOPMaster, $updatedAccruavalFromOPMaster->toArray());
        $dbAccruavalFromOPMaster = $this->accruavalFromOPMasterRepo->find($accruavalFromOPMaster->id);
        $this->assertModelData($fakeAccruavalFromOPMaster, $dbAccruavalFromOPMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->makeAccruavalFromOPMaster();
        $resp = $this->accruavalFromOPMasterRepo->delete($accruavalFromOPMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(AccruavalFromOPMaster::find($accruavalFromOPMaster->id), 'AccruavalFromOPMaster should not exist in DB');
    }
}
