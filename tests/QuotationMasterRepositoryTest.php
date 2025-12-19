<?php

use App\Models\QuotationMaster;
use App\Repositories\QuotationMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationMasterRepositoryTest extends TestCase
{
    use MakeQuotationMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationMasterRepository
     */
    protected $quotationMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quotationMasterRepo = App::make(QuotationMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuotationMaster()
    {
        $quotationMaster = $this->fakeQuotationMasterData();
        $createdQuotationMaster = $this->quotationMasterRepo->create($quotationMaster);
        $createdQuotationMaster = $createdQuotationMaster->toArray();
        $this->assertArrayHasKey('id', $createdQuotationMaster);
        $this->assertNotNull($createdQuotationMaster['id'], 'Created QuotationMaster must have id specified');
        $this->assertNotNull(QuotationMaster::find($createdQuotationMaster['id']), 'QuotationMaster with given id must be in DB');
        $this->assertModelData($quotationMaster, $createdQuotationMaster);
    }

    /**
     * @test read
     */
    public function testReadQuotationMaster()
    {
        $quotationMaster = $this->makeQuotationMaster();
        $dbQuotationMaster = $this->quotationMasterRepo->find($quotationMaster->id);
        $dbQuotationMaster = $dbQuotationMaster->toArray();
        $this->assertModelData($quotationMaster->toArray(), $dbQuotationMaster);
    }

    /**
     * @test update
     */
    public function testUpdateQuotationMaster()
    {
        $quotationMaster = $this->makeQuotationMaster();
        $fakeQuotationMaster = $this->fakeQuotationMasterData();
        $updatedQuotationMaster = $this->quotationMasterRepo->update($fakeQuotationMaster, $quotationMaster->id);
        $this->assertModelData($fakeQuotationMaster, $updatedQuotationMaster->toArray());
        $dbQuotationMaster = $this->quotationMasterRepo->find($quotationMaster->id);
        $this->assertModelData($fakeQuotationMaster, $dbQuotationMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuotationMaster()
    {
        $quotationMaster = $this->makeQuotationMaster();
        $resp = $this->quotationMasterRepo->delete($quotationMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(QuotationMaster::find($quotationMaster->id), 'QuotationMaster should not exist in DB');
    }
}
