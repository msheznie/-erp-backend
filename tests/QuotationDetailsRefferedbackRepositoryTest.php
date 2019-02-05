<?php

use App\Models\QuotationDetailsRefferedback;
use App\Repositories\QuotationDetailsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationDetailsRefferedbackRepositoryTest extends TestCase
{
    use MakeQuotationDetailsRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationDetailsRefferedbackRepository
     */
    protected $quotationDetailsRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quotationDetailsRefferedbackRepo = App::make(QuotationDetailsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->fakeQuotationDetailsRefferedbackData();
        $createdQuotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepo->create($quotationDetailsRefferedback);
        $createdQuotationDetailsRefferedback = $createdQuotationDetailsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdQuotationDetailsRefferedback);
        $this->assertNotNull($createdQuotationDetailsRefferedback['id'], 'Created QuotationDetailsRefferedback must have id specified');
        $this->assertNotNull(QuotationDetailsRefferedback::find($createdQuotationDetailsRefferedback['id']), 'QuotationDetailsRefferedback with given id must be in DB');
        $this->assertModelData($quotationDetailsRefferedback, $createdQuotationDetailsRefferedback);
    }

    /**
     * @test read
     */
    public function testReadQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->makeQuotationDetailsRefferedback();
        $dbQuotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepo->find($quotationDetailsRefferedback->id);
        $dbQuotationDetailsRefferedback = $dbQuotationDetailsRefferedback->toArray();
        $this->assertModelData($quotationDetailsRefferedback->toArray(), $dbQuotationDetailsRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->makeQuotationDetailsRefferedback();
        $fakeQuotationDetailsRefferedback = $this->fakeQuotationDetailsRefferedbackData();
        $updatedQuotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepo->update($fakeQuotationDetailsRefferedback, $quotationDetailsRefferedback->id);
        $this->assertModelData($fakeQuotationDetailsRefferedback, $updatedQuotationDetailsRefferedback->toArray());
        $dbQuotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepo->find($quotationDetailsRefferedback->id);
        $this->assertModelData($fakeQuotationDetailsRefferedback, $dbQuotationDetailsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->makeQuotationDetailsRefferedback();
        $resp = $this->quotationDetailsRefferedbackRepo->delete($quotationDetailsRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(QuotationDetailsRefferedback::find($quotationDetailsRefferedback->id), 'QuotationDetailsRefferedback should not exist in DB');
    }
}
