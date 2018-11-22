<?php

use App\Models\DirectReceiptDetailsRefferedHistory;
use App\Repositories\DirectReceiptDetailsRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectReceiptDetailsRefferedHistoryRepositoryTest extends TestCase
{
    use MakeDirectReceiptDetailsRefferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DirectReceiptDetailsRefferedHistoryRepository
     */
    protected $directReceiptDetailsRefferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->directReceiptDetailsRefferedHistoryRepo = App::make(DirectReceiptDetailsRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->fakeDirectReceiptDetailsRefferedHistoryData();
        $createdDirectReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepo->create($directReceiptDetailsRefferedHistory);
        $createdDirectReceiptDetailsRefferedHistory = $createdDirectReceiptDetailsRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdDirectReceiptDetailsRefferedHistory);
        $this->assertNotNull($createdDirectReceiptDetailsRefferedHistory['id'], 'Created DirectReceiptDetailsRefferedHistory must have id specified');
        $this->assertNotNull(DirectReceiptDetailsRefferedHistory::find($createdDirectReceiptDetailsRefferedHistory['id']), 'DirectReceiptDetailsRefferedHistory with given id must be in DB');
        $this->assertModelData($directReceiptDetailsRefferedHistory, $createdDirectReceiptDetailsRefferedHistory);
    }

    /**
     * @test read
     */
    public function testReadDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->makeDirectReceiptDetailsRefferedHistory();
        $dbDirectReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepo->find($directReceiptDetailsRefferedHistory->id);
        $dbDirectReceiptDetailsRefferedHistory = $dbDirectReceiptDetailsRefferedHistory->toArray();
        $this->assertModelData($directReceiptDetailsRefferedHistory->toArray(), $dbDirectReceiptDetailsRefferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdateDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->makeDirectReceiptDetailsRefferedHistory();
        $fakeDirectReceiptDetailsRefferedHistory = $this->fakeDirectReceiptDetailsRefferedHistoryData();
        $updatedDirectReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepo->update($fakeDirectReceiptDetailsRefferedHistory, $directReceiptDetailsRefferedHistory->id);
        $this->assertModelData($fakeDirectReceiptDetailsRefferedHistory, $updatedDirectReceiptDetailsRefferedHistory->toArray());
        $dbDirectReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepo->find($directReceiptDetailsRefferedHistory->id);
        $this->assertModelData($fakeDirectReceiptDetailsRefferedHistory, $dbDirectReceiptDetailsRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->makeDirectReceiptDetailsRefferedHistory();
        $resp = $this->directReceiptDetailsRefferedHistoryRepo->delete($directReceiptDetailsRefferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(DirectReceiptDetailsRefferedHistory::find($directReceiptDetailsRefferedHistory->id), 'DirectReceiptDetailsRefferedHistory should not exist in DB');
    }
}
