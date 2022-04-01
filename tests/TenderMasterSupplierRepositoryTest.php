<?php namespace Tests\Repositories;

use App\Models\TenderMasterSupplier;
use App\Repositories\TenderMasterSupplierRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderMasterSupplierRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderMasterSupplierRepository
     */
    protected $tenderMasterSupplierRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderMasterSupplierRepo = \App::make(TenderMasterSupplierRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->make()->toArray();

        $createdTenderMasterSupplier = $this->tenderMasterSupplierRepo->create($tenderMasterSupplier);

        $createdTenderMasterSupplier = $createdTenderMasterSupplier->toArray();
        $this->assertArrayHasKey('id', $createdTenderMasterSupplier);
        $this->assertNotNull($createdTenderMasterSupplier['id'], 'Created TenderMasterSupplier must have id specified');
        $this->assertNotNull(TenderMasterSupplier::find($createdTenderMasterSupplier['id']), 'TenderMasterSupplier with given id must be in DB');
        $this->assertModelData($tenderMasterSupplier, $createdTenderMasterSupplier);
    }

    /**
     * @test read
     */
    public function test_read_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->create();

        $dbTenderMasterSupplier = $this->tenderMasterSupplierRepo->find($tenderMasterSupplier->id);

        $dbTenderMasterSupplier = $dbTenderMasterSupplier->toArray();
        $this->assertModelData($tenderMasterSupplier->toArray(), $dbTenderMasterSupplier);
    }

    /**
     * @test update
     */
    public function test_update_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->create();
        $fakeTenderMasterSupplier = factory(TenderMasterSupplier::class)->make()->toArray();

        $updatedTenderMasterSupplier = $this->tenderMasterSupplierRepo->update($fakeTenderMasterSupplier, $tenderMasterSupplier->id);

        $this->assertModelData($fakeTenderMasterSupplier, $updatedTenderMasterSupplier->toArray());
        $dbTenderMasterSupplier = $this->tenderMasterSupplierRepo->find($tenderMasterSupplier->id);
        $this->assertModelData($fakeTenderMasterSupplier, $dbTenderMasterSupplier->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->create();

        $resp = $this->tenderMasterSupplierRepo->delete($tenderMasterSupplier->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderMasterSupplier::find($tenderMasterSupplier->id), 'TenderMasterSupplier should not exist in DB');
    }
}
