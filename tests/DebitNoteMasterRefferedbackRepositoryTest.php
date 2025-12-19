<?php

use App\Models\DebitNoteMasterRefferedback;
use App\Repositories\DebitNoteMasterRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteMasterRefferedbackRepositoryTest extends TestCase
{
    use MakeDebitNoteMasterRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DebitNoteMasterRefferedbackRepository
     */
    protected $debitNoteMasterRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->debitNoteMasterRefferedbackRepo = App::make(DebitNoteMasterRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->fakeDebitNoteMasterRefferedbackData();
        $createdDebitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepo->create($debitNoteMasterRefferedback);
        $createdDebitNoteMasterRefferedback = $createdDebitNoteMasterRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdDebitNoteMasterRefferedback);
        $this->assertNotNull($createdDebitNoteMasterRefferedback['id'], 'Created DebitNoteMasterRefferedback must have id specified');
        $this->assertNotNull(DebitNoteMasterRefferedback::find($createdDebitNoteMasterRefferedback['id']), 'DebitNoteMasterRefferedback with given id must be in DB');
        $this->assertModelData($debitNoteMasterRefferedback, $createdDebitNoteMasterRefferedback);
    }

    /**
     * @test read
     */
    public function testReadDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->makeDebitNoteMasterRefferedback();
        $dbDebitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepo->find($debitNoteMasterRefferedback->id);
        $dbDebitNoteMasterRefferedback = $dbDebitNoteMasterRefferedback->toArray();
        $this->assertModelData($debitNoteMasterRefferedback->toArray(), $dbDebitNoteMasterRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->makeDebitNoteMasterRefferedback();
        $fakeDebitNoteMasterRefferedback = $this->fakeDebitNoteMasterRefferedbackData();
        $updatedDebitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepo->update($fakeDebitNoteMasterRefferedback, $debitNoteMasterRefferedback->id);
        $this->assertModelData($fakeDebitNoteMasterRefferedback, $updatedDebitNoteMasterRefferedback->toArray());
        $dbDebitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepo->find($debitNoteMasterRefferedback->id);
        $this->assertModelData($fakeDebitNoteMasterRefferedback, $dbDebitNoteMasterRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->makeDebitNoteMasterRefferedback();
        $resp = $this->debitNoteMasterRefferedbackRepo->delete($debitNoteMasterRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(DebitNoteMasterRefferedback::find($debitNoteMasterRefferedback->id), 'DebitNoteMasterRefferedback should not exist in DB');
    }
}
