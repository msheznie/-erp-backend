<?php

use App\Models\DirectReceiptDetail;
use App\Repositories\DirectReceiptDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectReceiptDetailRepositoryTest extends TestCase
{
    use MakeDirectReceiptDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DirectReceiptDetailRepository
     */
    protected $directReceiptDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->directReceiptDetailRepo = App::make(DirectReceiptDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDirectReceiptDetail()
    {
        $directReceiptDetail = $this->fakeDirectReceiptDetailData();
        $createdDirectReceiptDetail = $this->directReceiptDetailRepo->create($directReceiptDetail);
        $createdDirectReceiptDetail = $createdDirectReceiptDetail->toArray();
        $this->assertArrayHasKey('id', $createdDirectReceiptDetail);
        $this->assertNotNull($createdDirectReceiptDetail['id'], 'Created DirectReceiptDetail must have id specified');
        $this->assertNotNull(DirectReceiptDetail::find($createdDirectReceiptDetail['id']), 'DirectReceiptDetail with given id must be in DB');
        $this->assertModelData($directReceiptDetail, $createdDirectReceiptDetail);
    }

    /**
     * @test read
     */
    public function testReadDirectReceiptDetail()
    {
        $directReceiptDetail = $this->makeDirectReceiptDetail();
        $dbDirectReceiptDetail = $this->directReceiptDetailRepo->find($directReceiptDetail->id);
        $dbDirectReceiptDetail = $dbDirectReceiptDetail->toArray();
        $this->assertModelData($directReceiptDetail->toArray(), $dbDirectReceiptDetail);
    }

    /**
     * @test update
     */
    public function testUpdateDirectReceiptDetail()
    {
        $directReceiptDetail = $this->makeDirectReceiptDetail();
        $fakeDirectReceiptDetail = $this->fakeDirectReceiptDetailData();
        $updatedDirectReceiptDetail = $this->directReceiptDetailRepo->update($fakeDirectReceiptDetail, $directReceiptDetail->id);
        $this->assertModelData($fakeDirectReceiptDetail, $updatedDirectReceiptDetail->toArray());
        $dbDirectReceiptDetail = $this->directReceiptDetailRepo->find($directReceiptDetail->id);
        $this->assertModelData($fakeDirectReceiptDetail, $dbDirectReceiptDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDirectReceiptDetail()
    {
        $directReceiptDetail = $this->makeDirectReceiptDetail();
        $resp = $this->directReceiptDetailRepo->delete($directReceiptDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(DirectReceiptDetail::find($directReceiptDetail->id), 'DirectReceiptDetail should not exist in DB');
    }
}
