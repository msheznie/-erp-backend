<?php

use App\Models\AssetDisposalDetail;
use App\Repositories\AssetDisposalDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalDetailRepositoryTest extends TestCase
{
    use MakeAssetDisposalDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetDisposalDetailRepository
     */
    protected $assetDisposalDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetDisposalDetailRepo = App::make(AssetDisposalDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->fakeAssetDisposalDetailData();
        $createdAssetDisposalDetail = $this->assetDisposalDetailRepo->create($assetDisposalDetail);
        $createdAssetDisposalDetail = $createdAssetDisposalDetail->toArray();
        $this->assertArrayHasKey('id', $createdAssetDisposalDetail);
        $this->assertNotNull($createdAssetDisposalDetail['id'], 'Created AssetDisposalDetail must have id specified');
        $this->assertNotNull(AssetDisposalDetail::find($createdAssetDisposalDetail['id']), 'AssetDisposalDetail with given id must be in DB');
        $this->assertModelData($assetDisposalDetail, $createdAssetDisposalDetail);
    }

    /**
     * @test read
     */
    public function testReadAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->makeAssetDisposalDetail();
        $dbAssetDisposalDetail = $this->assetDisposalDetailRepo->find($assetDisposalDetail->id);
        $dbAssetDisposalDetail = $dbAssetDisposalDetail->toArray();
        $this->assertModelData($assetDisposalDetail->toArray(), $dbAssetDisposalDetail);
    }

    /**
     * @test update
     */
    public function testUpdateAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->makeAssetDisposalDetail();
        $fakeAssetDisposalDetail = $this->fakeAssetDisposalDetailData();
        $updatedAssetDisposalDetail = $this->assetDisposalDetailRepo->update($fakeAssetDisposalDetail, $assetDisposalDetail->id);
        $this->assertModelData($fakeAssetDisposalDetail, $updatedAssetDisposalDetail->toArray());
        $dbAssetDisposalDetail = $this->assetDisposalDetailRepo->find($assetDisposalDetail->id);
        $this->assertModelData($fakeAssetDisposalDetail, $dbAssetDisposalDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->makeAssetDisposalDetail();
        $resp = $this->assetDisposalDetailRepo->delete($assetDisposalDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetDisposalDetail::find($assetDisposalDetail->id), 'AssetDisposalDetail should not exist in DB');
    }
}
