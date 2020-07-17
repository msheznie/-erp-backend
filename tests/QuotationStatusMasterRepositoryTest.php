<?php namespace Tests\Repositories;

use App\Models\QuotationStatusMaster;
use App\Repositories\QuotationStatusMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class QuotationStatusMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationStatusMasterRepository
     */
    protected $quotationStatusMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->quotationStatusMasterRepo = \App::make(QuotationStatusMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->make()->toArray();

        $createdQuotationStatusMaster = $this->quotationStatusMasterRepo->create($quotationStatusMaster);

        $createdQuotationStatusMaster = $createdQuotationStatusMaster->toArray();
        $this->assertArrayHasKey('id', $createdQuotationStatusMaster);
        $this->assertNotNull($createdQuotationStatusMaster['id'], 'Created QuotationStatusMaster must have id specified');
        $this->assertNotNull(QuotationStatusMaster::find($createdQuotationStatusMaster['id']), 'QuotationStatusMaster with given id must be in DB');
        $this->assertModelData($quotationStatusMaster, $createdQuotationStatusMaster);
    }

    /**
     * @test read
     */
    public function test_read_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->create();

        $dbQuotationStatusMaster = $this->quotationStatusMasterRepo->find($quotationStatusMaster->id);

        $dbQuotationStatusMaster = $dbQuotationStatusMaster->toArray();
        $this->assertModelData($quotationStatusMaster->toArray(), $dbQuotationStatusMaster);
    }

    /**
     * @test update
     */
    public function test_update_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->create();
        $fakeQuotationStatusMaster = factory(QuotationStatusMaster::class)->make()->toArray();

        $updatedQuotationStatusMaster = $this->quotationStatusMasterRepo->update($fakeQuotationStatusMaster, $quotationStatusMaster->id);

        $this->assertModelData($fakeQuotationStatusMaster, $updatedQuotationStatusMaster->toArray());
        $dbQuotationStatusMaster = $this->quotationStatusMasterRepo->find($quotationStatusMaster->id);
        $this->assertModelData($fakeQuotationStatusMaster, $dbQuotationStatusMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_quotation_status_master()
    {
        $quotationStatusMaster = factory(QuotationStatusMaster::class)->create();

        $resp = $this->quotationStatusMasterRepo->delete($quotationStatusMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(QuotationStatusMaster::find($quotationStatusMaster->id), 'QuotationStatusMaster should not exist in DB');
    }
}
