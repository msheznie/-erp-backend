<?php

use App\Models\QuotationMasterRefferedback;
use App\Repositories\QuotationMasterRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationMasterRefferedbackRepositoryTest extends TestCase
{
    use MakeQuotationMasterRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationMasterRefferedbackRepository
     */
    protected $quotationMasterRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quotationMasterRefferedbackRepo = App::make(QuotationMasterRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->fakeQuotationMasterRefferedbackData();
        $createdQuotationMasterRefferedback = $this->quotationMasterRefferedbackRepo->create($quotationMasterRefferedback);
        $createdQuotationMasterRefferedback = $createdQuotationMasterRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdQuotationMasterRefferedback);
        $this->assertNotNull($createdQuotationMasterRefferedback['id'], 'Created QuotationMasterRefferedback must have id specified');
        $this->assertNotNull(QuotationMasterRefferedback::find($createdQuotationMasterRefferedback['id']), 'QuotationMasterRefferedback with given id must be in DB');
        $this->assertModelData($quotationMasterRefferedback, $createdQuotationMasterRefferedback);
    }

    /**
     * @test read
     */
    public function testReadQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->makeQuotationMasterRefferedback();
        $dbQuotationMasterRefferedback = $this->quotationMasterRefferedbackRepo->find($quotationMasterRefferedback->id);
        $dbQuotationMasterRefferedback = $dbQuotationMasterRefferedback->toArray();
        $this->assertModelData($quotationMasterRefferedback->toArray(), $dbQuotationMasterRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->makeQuotationMasterRefferedback();
        $fakeQuotationMasterRefferedback = $this->fakeQuotationMasterRefferedbackData();
        $updatedQuotationMasterRefferedback = $this->quotationMasterRefferedbackRepo->update($fakeQuotationMasterRefferedback, $quotationMasterRefferedback->id);
        $this->assertModelData($fakeQuotationMasterRefferedback, $updatedQuotationMasterRefferedback->toArray());
        $dbQuotationMasterRefferedback = $this->quotationMasterRefferedbackRepo->find($quotationMasterRefferedback->id);
        $this->assertModelData($fakeQuotationMasterRefferedback, $dbQuotationMasterRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->makeQuotationMasterRefferedback();
        $resp = $this->quotationMasterRefferedbackRepo->delete($quotationMasterRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(QuotationMasterRefferedback::find($quotationMasterRefferedback->id), 'QuotationMasterRefferedback should not exist in DB');
    }
}
