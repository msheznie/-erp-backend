<?php

use App\Models\SalesPersonMaster;
use App\Repositories\SalesPersonMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalesPersonMasterRepositoryTest extends TestCase
{
    use MakeSalesPersonMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesPersonMasterRepository
     */
    protected $salesPersonMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->salesPersonMasterRepo = App::make(SalesPersonMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSalesPersonMaster()
    {
        $salesPersonMaster = $this->fakeSalesPersonMasterData();
        $createdSalesPersonMaster = $this->salesPersonMasterRepo->create($salesPersonMaster);
        $createdSalesPersonMaster = $createdSalesPersonMaster->toArray();
        $this->assertArrayHasKey('id', $createdSalesPersonMaster);
        $this->assertNotNull($createdSalesPersonMaster['id'], 'Created SalesPersonMaster must have id specified');
        $this->assertNotNull(SalesPersonMaster::find($createdSalesPersonMaster['id']), 'SalesPersonMaster with given id must be in DB');
        $this->assertModelData($salesPersonMaster, $createdSalesPersonMaster);
    }

    /**
     * @test read
     */
    public function testReadSalesPersonMaster()
    {
        $salesPersonMaster = $this->makeSalesPersonMaster();
        $dbSalesPersonMaster = $this->salesPersonMasterRepo->find($salesPersonMaster->id);
        $dbSalesPersonMaster = $dbSalesPersonMaster->toArray();
        $this->assertModelData($salesPersonMaster->toArray(), $dbSalesPersonMaster);
    }

    /**
     * @test update
     */
    public function testUpdateSalesPersonMaster()
    {
        $salesPersonMaster = $this->makeSalesPersonMaster();
        $fakeSalesPersonMaster = $this->fakeSalesPersonMasterData();
        $updatedSalesPersonMaster = $this->salesPersonMasterRepo->update($fakeSalesPersonMaster, $salesPersonMaster->id);
        $this->assertModelData($fakeSalesPersonMaster, $updatedSalesPersonMaster->toArray());
        $dbSalesPersonMaster = $this->salesPersonMasterRepo->find($salesPersonMaster->id);
        $this->assertModelData($fakeSalesPersonMaster, $dbSalesPersonMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSalesPersonMaster()
    {
        $salesPersonMaster = $this->makeSalesPersonMaster();
        $resp = $this->salesPersonMasterRepo->delete($salesPersonMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(SalesPersonMaster::find($salesPersonMaster->id), 'SalesPersonMaster should not exist in DB');
    }
}
