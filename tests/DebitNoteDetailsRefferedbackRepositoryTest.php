<?php

use App\Models\DebitNoteDetailsRefferedback;
use App\Repositories\DebitNoteDetailsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteDetailsRefferedbackRepositoryTest extends TestCase
{
    use MakeDebitNoteDetailsRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DebitNoteDetailsRefferedbackRepository
     */
    protected $debitNoteDetailsRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->debitNoteDetailsRefferedbackRepo = App::make(DebitNoteDetailsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->fakeDebitNoteDetailsRefferedbackData();
        $createdDebitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepo->create($debitNoteDetailsRefferedback);
        $createdDebitNoteDetailsRefferedback = $createdDebitNoteDetailsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdDebitNoteDetailsRefferedback);
        $this->assertNotNull($createdDebitNoteDetailsRefferedback['id'], 'Created DebitNoteDetailsRefferedback must have id specified');
        $this->assertNotNull(DebitNoteDetailsRefferedback::find($createdDebitNoteDetailsRefferedback['id']), 'DebitNoteDetailsRefferedback with given id must be in DB');
        $this->assertModelData($debitNoteDetailsRefferedback, $createdDebitNoteDetailsRefferedback);
    }

    /**
     * @test read
     */
    public function testReadDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->makeDebitNoteDetailsRefferedback();
        $dbDebitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepo->find($debitNoteDetailsRefferedback->id);
        $dbDebitNoteDetailsRefferedback = $dbDebitNoteDetailsRefferedback->toArray();
        $this->assertModelData($debitNoteDetailsRefferedback->toArray(), $dbDebitNoteDetailsRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->makeDebitNoteDetailsRefferedback();
        $fakeDebitNoteDetailsRefferedback = $this->fakeDebitNoteDetailsRefferedbackData();
        $updatedDebitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepo->update($fakeDebitNoteDetailsRefferedback, $debitNoteDetailsRefferedback->id);
        $this->assertModelData($fakeDebitNoteDetailsRefferedback, $updatedDebitNoteDetailsRefferedback->toArray());
        $dbDebitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepo->find($debitNoteDetailsRefferedback->id);
        $this->assertModelData($fakeDebitNoteDetailsRefferedback, $dbDebitNoteDetailsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->makeDebitNoteDetailsRefferedback();
        $resp = $this->debitNoteDetailsRefferedbackRepo->delete($debitNoteDetailsRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(DebitNoteDetailsRefferedback::find($debitNoteDetailsRefferedback->id), 'DebitNoteDetailsRefferedback should not exist in DB');
    }
}
