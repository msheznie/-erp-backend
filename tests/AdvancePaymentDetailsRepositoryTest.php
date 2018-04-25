<?php

use App\Models\AdvancePaymentDetails;
use App\Repositories\AdvancePaymentDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdvancePaymentDetailsRepositoryTest extends TestCase
{
    use MakeAdvancePaymentDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AdvancePaymentDetailsRepository
     */
    protected $advancePaymentDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->advancePaymentDetailsRepo = App::make(AdvancePaymentDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->fakeAdvancePaymentDetailsData();
        $createdAdvancePaymentDetails = $this->advancePaymentDetailsRepo->create($advancePaymentDetails);
        $createdAdvancePaymentDetails = $createdAdvancePaymentDetails->toArray();
        $this->assertArrayHasKey('id', $createdAdvancePaymentDetails);
        $this->assertNotNull($createdAdvancePaymentDetails['id'], 'Created AdvancePaymentDetails must have id specified');
        $this->assertNotNull(AdvancePaymentDetails::find($createdAdvancePaymentDetails['id']), 'AdvancePaymentDetails with given id must be in DB');
        $this->assertModelData($advancePaymentDetails, $createdAdvancePaymentDetails);
    }

    /**
     * @test read
     */
    public function testReadAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->makeAdvancePaymentDetails();
        $dbAdvancePaymentDetails = $this->advancePaymentDetailsRepo->find($advancePaymentDetails->id);
        $dbAdvancePaymentDetails = $dbAdvancePaymentDetails->toArray();
        $this->assertModelData($advancePaymentDetails->toArray(), $dbAdvancePaymentDetails);
    }

    /**
     * @test update
     */
    public function testUpdateAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->makeAdvancePaymentDetails();
        $fakeAdvancePaymentDetails = $this->fakeAdvancePaymentDetailsData();
        $updatedAdvancePaymentDetails = $this->advancePaymentDetailsRepo->update($fakeAdvancePaymentDetails, $advancePaymentDetails->id);
        $this->assertModelData($fakeAdvancePaymentDetails, $updatedAdvancePaymentDetails->toArray());
        $dbAdvancePaymentDetails = $this->advancePaymentDetailsRepo->find($advancePaymentDetails->id);
        $this->assertModelData($fakeAdvancePaymentDetails, $dbAdvancePaymentDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->makeAdvancePaymentDetails();
        $resp = $this->advancePaymentDetailsRepo->delete($advancePaymentDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(AdvancePaymentDetails::find($advancePaymentDetails->id), 'AdvancePaymentDetails should not exist in DB');
    }
}
