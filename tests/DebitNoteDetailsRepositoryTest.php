<?php

use App\Models\DebitNoteDetails;
use App\Repositories\DebitNoteDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteDetailsRepositoryTest extends TestCase
{
    use MakeDebitNoteDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DebitNoteDetailsRepository
     */
    protected $debitNoteDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->debitNoteDetailsRepo = App::make(DebitNoteDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDebitNoteDetails()
    {
        $debitNoteDetails = $this->fakeDebitNoteDetailsData();
        $createdDebitNoteDetails = $this->debitNoteDetailsRepo->create($debitNoteDetails);
        $createdDebitNoteDetails = $createdDebitNoteDetails->toArray();
        $this->assertArrayHasKey('id', $createdDebitNoteDetails);
        $this->assertNotNull($createdDebitNoteDetails['id'], 'Created DebitNoteDetails must have id specified');
        $this->assertNotNull(DebitNoteDetails::find($createdDebitNoteDetails['id']), 'DebitNoteDetails with given id must be in DB');
        $this->assertModelData($debitNoteDetails, $createdDebitNoteDetails);
    }

    /**
     * @test read
     */
    public function testReadDebitNoteDetails()
    {
        $debitNoteDetails = $this->makeDebitNoteDetails();
        $dbDebitNoteDetails = $this->debitNoteDetailsRepo->find($debitNoteDetails->id);
        $dbDebitNoteDetails = $dbDebitNoteDetails->toArray();
        $this->assertModelData($debitNoteDetails->toArray(), $dbDebitNoteDetails);
    }

    /**
     * @test update
     */
    public function testUpdateDebitNoteDetails()
    {
        $debitNoteDetails = $this->makeDebitNoteDetails();
        $fakeDebitNoteDetails = $this->fakeDebitNoteDetailsData();
        $updatedDebitNoteDetails = $this->debitNoteDetailsRepo->update($fakeDebitNoteDetails, $debitNoteDetails->id);
        $this->assertModelData($fakeDebitNoteDetails, $updatedDebitNoteDetails->toArray());
        $dbDebitNoteDetails = $this->debitNoteDetailsRepo->find($debitNoteDetails->id);
        $this->assertModelData($fakeDebitNoteDetails, $dbDebitNoteDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDebitNoteDetails()
    {
        $debitNoteDetails = $this->makeDebitNoteDetails();
        $resp = $this->debitNoteDetailsRepo->delete($debitNoteDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(DebitNoteDetails::find($debitNoteDetails->id), 'DebitNoteDetails should not exist in DB');
    }
}
