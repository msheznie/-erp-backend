<?php

use App\Models\CreditNote;
use App\Repositories\CreditNoteRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteRepositoryTest extends TestCase
{
    use MakeCreditNoteTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CreditNoteRepository
     */
    protected $creditNoteRepo;

    public function setUp()
    {
        parent::setUp();
        $this->creditNoteRepo = App::make(CreditNoteRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCreditNote()
    {
        $creditNote = $this->fakeCreditNoteData();
        $createdCreditNote = $this->creditNoteRepo->create($creditNote);
        $createdCreditNote = $createdCreditNote->toArray();
        $this->assertArrayHasKey('id', $createdCreditNote);
        $this->assertNotNull($createdCreditNote['id'], 'Created CreditNote must have id specified');
        $this->assertNotNull(CreditNote::find($createdCreditNote['id']), 'CreditNote with given id must be in DB');
        $this->assertModelData($creditNote, $createdCreditNote);
    }

    /**
     * @test read
     */
    public function testReadCreditNote()
    {
        $creditNote = $this->makeCreditNote();
        $dbCreditNote = $this->creditNoteRepo->find($creditNote->id);
        $dbCreditNote = $dbCreditNote->toArray();
        $this->assertModelData($creditNote->toArray(), $dbCreditNote);
    }

    /**
     * @test update
     */
    public function testUpdateCreditNote()
    {
        $creditNote = $this->makeCreditNote();
        $fakeCreditNote = $this->fakeCreditNoteData();
        $updatedCreditNote = $this->creditNoteRepo->update($fakeCreditNote, $creditNote->id);
        $this->assertModelData($fakeCreditNote, $updatedCreditNote->toArray());
        $dbCreditNote = $this->creditNoteRepo->find($creditNote->id);
        $this->assertModelData($fakeCreditNote, $dbCreditNote->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCreditNote()
    {
        $creditNote = $this->makeCreditNote();
        $resp = $this->creditNoteRepo->delete($creditNote->id);
        $this->assertTrue($resp);
        $this->assertNull(CreditNote::find($creditNote->id), 'CreditNote should not exist in DB');
    }
}
