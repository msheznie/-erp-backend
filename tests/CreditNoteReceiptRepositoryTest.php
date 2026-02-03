<?php namespace Tests\Repositories;

use App\Models\CreditNoteReceipt;
use App\Repositories\CreditNoteReceiptRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CreditNoteReceiptRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CreditNoteReceiptRepository
     */
    protected $creditNoteReceiptRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->creditNoteReceiptRepo = \App::make(CreditNoteReceiptRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->make()->toArray();

        $createdCreditNoteReceipt = $this->creditNoteReceiptRepo->create($creditNoteReceipt);

        $createdCreditNoteReceipt = $createdCreditNoteReceipt->toArray();
        $this->assertArrayHasKey('id', $createdCreditNoteReceipt);
        $this->assertNotNull($createdCreditNoteReceipt['id'], 'Created CreditNoteReceipt must have id specified');
        $this->assertNotNull(CreditNoteReceipt::find($createdCreditNoteReceipt['id']), 'CreditNoteReceipt with given id must be in DB');
        $this->assertModelData($creditNoteReceipt, $createdCreditNoteReceipt);
    }

    /**
     * @test read
     */
    public function test_read_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->create();

        $dbCreditNoteReceipt = $this->creditNoteReceiptRepo->find($creditNoteReceipt->id);

        $dbCreditNoteReceipt = $dbCreditNoteReceipt->toArray();
        $this->assertModelData($creditNoteReceipt->toArray(), $dbCreditNoteReceipt);
    }

    /**
     * @test update
     */
    public function test_update_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->create();
        $fakeCreditNoteReceipt = factory(CreditNoteReceipt::class)->make()->toArray();

        $updatedCreditNoteReceipt = $this->creditNoteReceiptRepo->update($fakeCreditNoteReceipt, $creditNoteReceipt->id);

        $this->assertModelData($fakeCreditNoteReceipt, $updatedCreditNoteReceipt->toArray());
        $dbCreditNoteReceipt = $this->creditNoteReceiptRepo->find($creditNoteReceipt->id);
        $this->assertModelData($fakeCreditNoteReceipt, $dbCreditNoteReceipt->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->create();

        $resp = $this->creditNoteReceiptRepo->delete($creditNoteReceipt->id);

        $this->assertTrue($resp);
        $this->assertNull(CreditNoteReceipt::find($creditNoteReceipt->id), 'CreditNoteReceipt should not exist in DB');
    }
}
