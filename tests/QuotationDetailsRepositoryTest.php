<?php

use App\Models\QuotationDetails;
use App\Repositories\QuotationDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationDetailsRepositoryTest extends TestCase
{
    use MakeQuotationDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationDetailsRepository
     */
    protected $quotationDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quotationDetailsRepo = App::make(QuotationDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuotationDetails()
    {
        $quotationDetails = $this->fakeQuotationDetailsData();
        $createdQuotationDetails = $this->quotationDetailsRepo->create($quotationDetails);
        $createdQuotationDetails = $createdQuotationDetails->toArray();
        $this->assertArrayHasKey('id', $createdQuotationDetails);
        $this->assertNotNull($createdQuotationDetails['id'], 'Created QuotationDetails must have id specified');
        $this->assertNotNull(QuotationDetails::find($createdQuotationDetails['id']), 'QuotationDetails with given id must be in DB');
        $this->assertModelData($quotationDetails, $createdQuotationDetails);
    }

    /**
     * @test read
     */
    public function testReadQuotationDetails()
    {
        $quotationDetails = $this->makeQuotationDetails();
        $dbQuotationDetails = $this->quotationDetailsRepo->find($quotationDetails->id);
        $dbQuotationDetails = $dbQuotationDetails->toArray();
        $this->assertModelData($quotationDetails->toArray(), $dbQuotationDetails);
    }

    /**
     * @test update
     */
    public function testUpdateQuotationDetails()
    {
        $quotationDetails = $this->makeQuotationDetails();
        $fakeQuotationDetails = $this->fakeQuotationDetailsData();
        $updatedQuotationDetails = $this->quotationDetailsRepo->update($fakeQuotationDetails, $quotationDetails->id);
        $this->assertModelData($fakeQuotationDetails, $updatedQuotationDetails->toArray());
        $dbQuotationDetails = $this->quotationDetailsRepo->find($quotationDetails->id);
        $this->assertModelData($fakeQuotationDetails, $dbQuotationDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuotationDetails()
    {
        $quotationDetails = $this->makeQuotationDetails();
        $resp = $this->quotationDetailsRepo->delete($quotationDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(QuotationDetails::find($quotationDetails->id), 'QuotationDetails should not exist in DB');
    }
}
