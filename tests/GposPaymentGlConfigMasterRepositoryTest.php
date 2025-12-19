<?php

use App\Models\GposPaymentGlConfigMaster;
use App\Repositories\GposPaymentGlConfigMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposPaymentGlConfigMasterRepositoryTest extends TestCase
{
    use MakeGposPaymentGlConfigMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GposPaymentGlConfigMasterRepository
     */
    protected $gposPaymentGlConfigMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gposPaymentGlConfigMasterRepo = App::make(GposPaymentGlConfigMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->fakeGposPaymentGlConfigMasterData();
        $createdGposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepo->create($gposPaymentGlConfigMaster);
        $createdGposPaymentGlConfigMaster = $createdGposPaymentGlConfigMaster->toArray();
        $this->assertArrayHasKey('id', $createdGposPaymentGlConfigMaster);
        $this->assertNotNull($createdGposPaymentGlConfigMaster['id'], 'Created GposPaymentGlConfigMaster must have id specified');
        $this->assertNotNull(GposPaymentGlConfigMaster::find($createdGposPaymentGlConfigMaster['id']), 'GposPaymentGlConfigMaster with given id must be in DB');
        $this->assertModelData($gposPaymentGlConfigMaster, $createdGposPaymentGlConfigMaster);
    }

    /**
     * @test read
     */
    public function testReadGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->makeGposPaymentGlConfigMaster();
        $dbGposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepo->find($gposPaymentGlConfigMaster->id);
        $dbGposPaymentGlConfigMaster = $dbGposPaymentGlConfigMaster->toArray();
        $this->assertModelData($gposPaymentGlConfigMaster->toArray(), $dbGposPaymentGlConfigMaster);
    }

    /**
     * @test update
     */
    public function testUpdateGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->makeGposPaymentGlConfigMaster();
        $fakeGposPaymentGlConfigMaster = $this->fakeGposPaymentGlConfigMasterData();
        $updatedGposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepo->update($fakeGposPaymentGlConfigMaster, $gposPaymentGlConfigMaster->id);
        $this->assertModelData($fakeGposPaymentGlConfigMaster, $updatedGposPaymentGlConfigMaster->toArray());
        $dbGposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepo->find($gposPaymentGlConfigMaster->id);
        $this->assertModelData($fakeGposPaymentGlConfigMaster, $dbGposPaymentGlConfigMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->makeGposPaymentGlConfigMaster();
        $resp = $this->gposPaymentGlConfigMasterRepo->delete($gposPaymentGlConfigMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(GposPaymentGlConfigMaster::find($gposPaymentGlConfigMaster->id), 'GposPaymentGlConfigMaster should not exist in DB');
    }
}
