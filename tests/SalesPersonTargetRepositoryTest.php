<?php

use App\Models\SalesPersonTarget;
use App\Repositories\SalesPersonTargetRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalesPersonTargetRepositoryTest extends TestCase
{
    use MakeSalesPersonTargetTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesPersonTargetRepository
     */
    protected $salesPersonTargetRepo;

    public function setUp()
    {
        parent::setUp();
        $this->salesPersonTargetRepo = App::make(SalesPersonTargetRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSalesPersonTarget()
    {
        $salesPersonTarget = $this->fakeSalesPersonTargetData();
        $createdSalesPersonTarget = $this->salesPersonTargetRepo->create($salesPersonTarget);
        $createdSalesPersonTarget = $createdSalesPersonTarget->toArray();
        $this->assertArrayHasKey('id', $createdSalesPersonTarget);
        $this->assertNotNull($createdSalesPersonTarget['id'], 'Created SalesPersonTarget must have id specified');
        $this->assertNotNull(SalesPersonTarget::find($createdSalesPersonTarget['id']), 'SalesPersonTarget with given id must be in DB');
        $this->assertModelData($salesPersonTarget, $createdSalesPersonTarget);
    }

    /**
     * @test read
     */
    public function testReadSalesPersonTarget()
    {
        $salesPersonTarget = $this->makeSalesPersonTarget();
        $dbSalesPersonTarget = $this->salesPersonTargetRepo->find($salesPersonTarget->id);
        $dbSalesPersonTarget = $dbSalesPersonTarget->toArray();
        $this->assertModelData($salesPersonTarget->toArray(), $dbSalesPersonTarget);
    }

    /**
     * @test update
     */
    public function testUpdateSalesPersonTarget()
    {
        $salesPersonTarget = $this->makeSalesPersonTarget();
        $fakeSalesPersonTarget = $this->fakeSalesPersonTargetData();
        $updatedSalesPersonTarget = $this->salesPersonTargetRepo->update($fakeSalesPersonTarget, $salesPersonTarget->id);
        $this->assertModelData($fakeSalesPersonTarget, $updatedSalesPersonTarget->toArray());
        $dbSalesPersonTarget = $this->salesPersonTargetRepo->find($salesPersonTarget->id);
        $this->assertModelData($fakeSalesPersonTarget, $dbSalesPersonTarget->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSalesPersonTarget()
    {
        $salesPersonTarget = $this->makeSalesPersonTarget();
        $resp = $this->salesPersonTargetRepo->delete($salesPersonTarget->id);
        $this->assertTrue($resp);
        $this->assertNull(SalesPersonTarget::find($salesPersonTarget->id), 'SalesPersonTarget should not exist in DB');
    }
}
