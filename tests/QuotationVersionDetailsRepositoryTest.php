<?php

use App\Models\QuotationVersionDetails;
use App\Repositories\QuotationVersionDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationVersionDetailsRepositoryTest extends TestCase
{
    use MakeQuotationVersionDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationVersionDetailsRepository
     */
    protected $quotationVersionDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quotationVersionDetailsRepo = App::make(QuotationVersionDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->fakeQuotationVersionDetailsData();
        $createdQuotationVersionDetails = $this->quotationVersionDetailsRepo->create($quotationVersionDetails);
        $createdQuotationVersionDetails = $createdQuotationVersionDetails->toArray();
        $this->assertArrayHasKey('id', $createdQuotationVersionDetails);
        $this->assertNotNull($createdQuotationVersionDetails['id'], 'Created QuotationVersionDetails must have id specified');
        $this->assertNotNull(QuotationVersionDetails::find($createdQuotationVersionDetails['id']), 'QuotationVersionDetails with given id must be in DB');
        $this->assertModelData($quotationVersionDetails, $createdQuotationVersionDetails);
    }

    /**
     * @test read
     */
    public function testReadQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->makeQuotationVersionDetails();
        $dbQuotationVersionDetails = $this->quotationVersionDetailsRepo->find($quotationVersionDetails->id);
        $dbQuotationVersionDetails = $dbQuotationVersionDetails->toArray();
        $this->assertModelData($quotationVersionDetails->toArray(), $dbQuotationVersionDetails);
    }

    /**
     * @test update
     */
    public function testUpdateQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->makeQuotationVersionDetails();
        $fakeQuotationVersionDetails = $this->fakeQuotationVersionDetailsData();
        $updatedQuotationVersionDetails = $this->quotationVersionDetailsRepo->update($fakeQuotationVersionDetails, $quotationVersionDetails->id);
        $this->assertModelData($fakeQuotationVersionDetails, $updatedQuotationVersionDetails->toArray());
        $dbQuotationVersionDetails = $this->quotationVersionDetailsRepo->find($quotationVersionDetails->id);
        $this->assertModelData($fakeQuotationVersionDetails, $dbQuotationVersionDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->makeQuotationVersionDetails();
        $resp = $this->quotationVersionDetailsRepo->delete($quotationVersionDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(QuotationVersionDetails::find($quotationVersionDetails->id), 'QuotationVersionDetails should not exist in DB');
    }
}
