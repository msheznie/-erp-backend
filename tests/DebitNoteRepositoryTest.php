<?php

use App\Models\DebitNote;
use App\Repositories\DebitNoteRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteRepositoryTest extends TestCase
{
    use MakeDebitNoteTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DebitNoteRepository
     */
    protected $debitNoteRepo;

    public function setUp()
    {
        parent::setUp();
        $this->debitNoteRepo = App::make(DebitNoteRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDebitNote()
    {
        $debitNote = $this->fakeDebitNoteData();
        $createdDebitNote = $this->debitNoteRepo->create($debitNote);
        $createdDebitNote = $createdDebitNote->toArray();
        $this->assertArrayHasKey('id', $createdDebitNote);
        $this->assertNotNull($createdDebitNote['id'], 'Created DebitNote must have id specified');
        $this->assertNotNull(DebitNote::find($createdDebitNote['id']), 'DebitNote with given id must be in DB');
        $this->assertModelData($debitNote, $createdDebitNote);
    }

    /**
     * @test read
     */
    public function testReadDebitNote()
    {
        $debitNote = $this->makeDebitNote();
        $dbDebitNote = $this->debitNoteRepo->find($debitNote->id);
        $dbDebitNote = $dbDebitNote->toArray();
        $this->assertModelData($debitNote->toArray(), $dbDebitNote);
    }

    /**
     * @test update
     */
    public function testUpdateDebitNote()
    {
        $debitNote = $this->makeDebitNote();
        $fakeDebitNote = $this->fakeDebitNoteData();
        $updatedDebitNote = $this->debitNoteRepo->update($fakeDebitNote, $debitNote->id);
        $this->assertModelData($fakeDebitNote, $updatedDebitNote->toArray());
        $dbDebitNote = $this->debitNoteRepo->find($debitNote->id);
        $this->assertModelData($fakeDebitNote, $dbDebitNote->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDebitNote()
    {
        $debitNote = $this->makeDebitNote();
        $resp = $this->debitNoteRepo->delete($debitNote->id);
        $this->assertTrue($resp);
        $this->assertNull(DebitNote::find($debitNote->id), 'DebitNote should not exist in DB');
    }
}
