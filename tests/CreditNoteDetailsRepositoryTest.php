<?php

use App\Models\CreditNoteDetails;
use App\Repositories\CreditNoteDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteDetailsRepositoryTest extends TestCase
{
    use MakeCreditNoteDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CreditNoteDetailsRepository
     */
    protected $creditNoteDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->creditNoteDetailsRepo = App::make(CreditNoteDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCreditNoteDetails()
    {
        $creditNoteDetails = $this->fakeCreditNoteDetailsData();
        $createdCreditNoteDetails = $this->creditNoteDetailsRepo->create($creditNoteDetails);
        $createdCreditNoteDetails = $createdCreditNoteDetails->toArray();
        $this->assertArrayHasKey('id', $createdCreditNoteDetails);
        $this->assertNotNull($createdCreditNoteDetails['id'], 'Created CreditNoteDetails must have id specified');
        $this->assertNotNull(CreditNoteDetails::find($createdCreditNoteDetails['id']), 'CreditNoteDetails with given id must be in DB');
        $this->assertModelData($creditNoteDetails, $createdCreditNoteDetails);
    }

    /**
     * @test read
     */
    public function testReadCreditNoteDetails()
    {
        $creditNoteDetails = $this->makeCreditNoteDetails();
        $dbCreditNoteDetails = $this->creditNoteDetailsRepo->find($creditNoteDetails->id);
        $dbCreditNoteDetails = $dbCreditNoteDetails->toArray();
        $this->assertModelData($creditNoteDetails->toArray(), $dbCreditNoteDetails);
    }

    /**
     * @test update
     */
    public function testUpdateCreditNoteDetails()
    {
        $creditNoteDetails = $this->makeCreditNoteDetails();
        $fakeCreditNoteDetails = $this->fakeCreditNoteDetailsData();
        $updatedCreditNoteDetails = $this->creditNoteDetailsRepo->update($fakeCreditNoteDetails, $creditNoteDetails->id);
        $this->assertModelData($fakeCreditNoteDetails, $updatedCreditNoteDetails->toArray());
        $dbCreditNoteDetails = $this->creditNoteDetailsRepo->find($creditNoteDetails->id);
        $this->assertModelData($fakeCreditNoteDetails, $dbCreditNoteDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCreditNoteDetails()
    {
        $creditNoteDetails = $this->makeCreditNoteDetails();
        $resp = $this->creditNoteDetailsRepo->delete($creditNoteDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(CreditNoteDetails::find($creditNoteDetails->id), 'CreditNoteDetails should not exist in DB');
    }
}
