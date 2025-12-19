<?php

use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Repositories\CustReceivePaymentDetRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustReceivePaymentDetRefferedHistoryRepositoryTest extends TestCase
{
    use MakeCustReceivePaymentDetRefferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustReceivePaymentDetRefferedHistoryRepository
     */
    protected $custReceivePaymentDetRefferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->custReceivePaymentDetRefferedHistoryRepo = App::make(CustReceivePaymentDetRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->fakeCustReceivePaymentDetRefferedHistoryData();
        $createdCustReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepo->create($custReceivePaymentDetRefferedHistory);
        $createdCustReceivePaymentDetRefferedHistory = $createdCustReceivePaymentDetRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdCustReceivePaymentDetRefferedHistory);
        $this->assertNotNull($createdCustReceivePaymentDetRefferedHistory['id'], 'Created CustReceivePaymentDetRefferedHistory must have id specified');
        $this->assertNotNull(CustReceivePaymentDetRefferedHistory::find($createdCustReceivePaymentDetRefferedHistory['id']), 'CustReceivePaymentDetRefferedHistory with given id must be in DB');
        $this->assertModelData($custReceivePaymentDetRefferedHistory, $createdCustReceivePaymentDetRefferedHistory);
    }

    /**
     * @test read
     */
    public function testReadCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->makeCustReceivePaymentDetRefferedHistory();
        $dbCustReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepo->find($custReceivePaymentDetRefferedHistory->id);
        $dbCustReceivePaymentDetRefferedHistory = $dbCustReceivePaymentDetRefferedHistory->toArray();
        $this->assertModelData($custReceivePaymentDetRefferedHistory->toArray(), $dbCustReceivePaymentDetRefferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdateCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->makeCustReceivePaymentDetRefferedHistory();
        $fakeCustReceivePaymentDetRefferedHistory = $this->fakeCustReceivePaymentDetRefferedHistoryData();
        $updatedCustReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepo->update($fakeCustReceivePaymentDetRefferedHistory, $custReceivePaymentDetRefferedHistory->id);
        $this->assertModelData($fakeCustReceivePaymentDetRefferedHistory, $updatedCustReceivePaymentDetRefferedHistory->toArray());
        $dbCustReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepo->find($custReceivePaymentDetRefferedHistory->id);
        $this->assertModelData($fakeCustReceivePaymentDetRefferedHistory, $dbCustReceivePaymentDetRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->makeCustReceivePaymentDetRefferedHistory();
        $resp = $this->custReceivePaymentDetRefferedHistoryRepo->delete($custReceivePaymentDetRefferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(CustReceivePaymentDetRefferedHistory::find($custReceivePaymentDetRefferedHistory->id), 'CustReceivePaymentDetRefferedHistory should not exist in DB');
    }
}
