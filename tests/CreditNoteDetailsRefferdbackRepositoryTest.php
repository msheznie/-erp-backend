<?php

use App\Models\CreditNoteDetailsRefferdback;
use App\Repositories\CreditNoteDetailsRefferdbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteDetailsRefferdbackRepositoryTest extends TestCase
{
    use MakeCreditNoteDetailsRefferdbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CreditNoteDetailsRefferdbackRepository
     */
    protected $creditNoteDetailsRefferdbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->creditNoteDetailsRefferdbackRepo = App::make(CreditNoteDetailsRefferdbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->fakeCreditNoteDetailsRefferdbackData();
        $createdCreditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepo->create($creditNoteDetailsRefferdback);
        $createdCreditNoteDetailsRefferdback = $createdCreditNoteDetailsRefferdback->toArray();
        $this->assertArrayHasKey('id', $createdCreditNoteDetailsRefferdback);
        $this->assertNotNull($createdCreditNoteDetailsRefferdback['id'], 'Created CreditNoteDetailsRefferdback must have id specified');
        $this->assertNotNull(CreditNoteDetailsRefferdback::find($createdCreditNoteDetailsRefferdback['id']), 'CreditNoteDetailsRefferdback with given id must be in DB');
        $this->assertModelData($creditNoteDetailsRefferdback, $createdCreditNoteDetailsRefferdback);
    }

    /**
     * @test read
     */
    public function testReadCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->makeCreditNoteDetailsRefferdback();
        $dbCreditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepo->find($creditNoteDetailsRefferdback->id);
        $dbCreditNoteDetailsRefferdback = $dbCreditNoteDetailsRefferdback->toArray();
        $this->assertModelData($creditNoteDetailsRefferdback->toArray(), $dbCreditNoteDetailsRefferdback);
    }

    /**
     * @test update
     */
    public function testUpdateCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->makeCreditNoteDetailsRefferdback();
        $fakeCreditNoteDetailsRefferdback = $this->fakeCreditNoteDetailsRefferdbackData();
        $updatedCreditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepo->update($fakeCreditNoteDetailsRefferdback, $creditNoteDetailsRefferdback->id);
        $this->assertModelData($fakeCreditNoteDetailsRefferdback, $updatedCreditNoteDetailsRefferdback->toArray());
        $dbCreditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepo->find($creditNoteDetailsRefferdback->id);
        $this->assertModelData($fakeCreditNoteDetailsRefferdback, $dbCreditNoteDetailsRefferdback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->makeCreditNoteDetailsRefferdback();
        $resp = $this->creditNoteDetailsRefferdbackRepo->delete($creditNoteDetailsRefferdback->id);
        $this->assertTrue($resp);
        $this->assertNull(CreditNoteDetailsRefferdback::find($creditNoteDetailsRefferdback->id), 'CreditNoteDetailsRefferdback should not exist in DB');
    }
}
