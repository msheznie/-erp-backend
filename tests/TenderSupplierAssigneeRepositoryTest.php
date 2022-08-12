<?php namespace Tests\Repositories;

use App\Models\TenderSupplierAssignee;
use App\Repositories\TenderSupplierAssigneeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderSupplierAssigneeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderSupplierAssigneeRepository
     */
    protected $tenderSupplierAssigneeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderSupplierAssigneeRepo = \App::make(TenderSupplierAssigneeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->make()->toArray();

        $createdTenderSupplierAssignee = $this->tenderSupplierAssigneeRepo->create($tenderSupplierAssignee);

        $createdTenderSupplierAssignee = $createdTenderSupplierAssignee->toArray();
        $this->assertArrayHasKey('id', $createdTenderSupplierAssignee);
        $this->assertNotNull($createdTenderSupplierAssignee['id'], 'Created TenderSupplierAssignee must have id specified');
        $this->assertNotNull(TenderSupplierAssignee::find($createdTenderSupplierAssignee['id']), 'TenderSupplierAssignee with given id must be in DB');
        $this->assertModelData($tenderSupplierAssignee, $createdTenderSupplierAssignee);
    }

    /**
     * @test read
     */
    public function test_read_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->create();

        $dbTenderSupplierAssignee = $this->tenderSupplierAssigneeRepo->find($tenderSupplierAssignee->id);

        $dbTenderSupplierAssignee = $dbTenderSupplierAssignee->toArray();
        $this->assertModelData($tenderSupplierAssignee->toArray(), $dbTenderSupplierAssignee);
    }

    /**
     * @test update
     */
    public function test_update_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->create();
        $fakeTenderSupplierAssignee = factory(TenderSupplierAssignee::class)->make()->toArray();

        $updatedTenderSupplierAssignee = $this->tenderSupplierAssigneeRepo->update($fakeTenderSupplierAssignee, $tenderSupplierAssignee->id);

        $this->assertModelData($fakeTenderSupplierAssignee, $updatedTenderSupplierAssignee->toArray());
        $dbTenderSupplierAssignee = $this->tenderSupplierAssigneeRepo->find($tenderSupplierAssignee->id);
        $this->assertModelData($fakeTenderSupplierAssignee, $dbTenderSupplierAssignee->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->create();

        $resp = $this->tenderSupplierAssigneeRepo->delete($tenderSupplierAssignee->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderSupplierAssignee::find($tenderSupplierAssignee->id), 'TenderSupplierAssignee should not exist in DB');
    }
}
