<?php

use App\Models\CreditNoteReferredback;
use App\Repositories\CreditNoteReferredbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteReferredbackRepositoryTest extends TestCase
{
    use MakeCreditNoteReferredbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CreditNoteReferredbackRepository
     */
    protected $creditNoteReferredbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->creditNoteReferredbackRepo = App::make(CreditNoteReferredbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->fakeCreditNoteReferredbackData();
        $createdCreditNoteReferredback = $this->creditNoteReferredbackRepo->create($creditNoteReferredback);
        $createdCreditNoteReferredback = $createdCreditNoteReferredback->toArray();
        $this->assertArrayHasKey('id', $createdCreditNoteReferredback);
        $this->assertNotNull($createdCreditNoteReferredback['id'], 'Created CreditNoteReferredback must have id specified');
        $this->assertNotNull(CreditNoteReferredback::find($createdCreditNoteReferredback['id']), 'CreditNoteReferredback with given id must be in DB');
        $this->assertModelData($creditNoteReferredback, $createdCreditNoteReferredback);
    }

    /**
     * @test read
     */
    public function testReadCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->makeCreditNoteReferredback();
        $dbCreditNoteReferredback = $this->creditNoteReferredbackRepo->find($creditNoteReferredback->id);
        $dbCreditNoteReferredback = $dbCreditNoteReferredback->toArray();
        $this->assertModelData($creditNoteReferredback->toArray(), $dbCreditNoteReferredback);
    }

    /**
     * @test update
     */
    public function testUpdateCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->makeCreditNoteReferredback();
        $fakeCreditNoteReferredback = $this->fakeCreditNoteReferredbackData();
        $updatedCreditNoteReferredback = $this->creditNoteReferredbackRepo->update($fakeCreditNoteReferredback, $creditNoteReferredback->id);
        $this->assertModelData($fakeCreditNoteReferredback, $updatedCreditNoteReferredback->toArray());
        $dbCreditNoteReferredback = $this->creditNoteReferredbackRepo->find($creditNoteReferredback->id);
        $this->assertModelData($fakeCreditNoteReferredback, $dbCreditNoteReferredback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->makeCreditNoteReferredback();
        $resp = $this->creditNoteReferredbackRepo->delete($creditNoteReferredback->id);
        $this->assertTrue($resp);
        $this->assertNull(CreditNoteReferredback::find($creditNoteReferredback->id), 'CreditNoteReferredback should not exist in DB');
    }
}
