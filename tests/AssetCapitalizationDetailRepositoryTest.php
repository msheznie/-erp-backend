<?php

use App\Models\AssetCapitalizationDetail;
use App\Repositories\AssetCapitalizationDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizationDetailRepositoryTest extends TestCase
{
    use MakeAssetCapitalizationDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetCapitalizationDetailRepository
     */
    protected $assetCapitalizationDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetCapitalizationDetailRepo = App::make(AssetCapitalizationDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->fakeAssetCapitalizationDetailData();
        $createdAssetCapitalizationDetail = $this->assetCapitalizationDetailRepo->create($assetCapitalizationDetail);
        $createdAssetCapitalizationDetail = $createdAssetCapitalizationDetail->toArray();
        $this->assertArrayHasKey('id', $createdAssetCapitalizationDetail);
        $this->assertNotNull($createdAssetCapitalizationDetail['id'], 'Created AssetCapitalizationDetail must have id specified');
        $this->assertNotNull(AssetCapitalizationDetail::find($createdAssetCapitalizationDetail['id']), 'AssetCapitalizationDetail with given id must be in DB');
        $this->assertModelData($assetCapitalizationDetail, $createdAssetCapitalizationDetail);
    }

    /**
     * @test read
     */
    public function testReadAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->makeAssetCapitalizationDetail();
        $dbAssetCapitalizationDetail = $this->assetCapitalizationDetailRepo->find($assetCapitalizationDetail->id);
        $dbAssetCapitalizationDetail = $dbAssetCapitalizationDetail->toArray();
        $this->assertModelData($assetCapitalizationDetail->toArray(), $dbAssetCapitalizationDetail);
    }

    /**
     * @test update
     */
    public function testUpdateAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->makeAssetCapitalizationDetail();
        $fakeAssetCapitalizationDetail = $this->fakeAssetCapitalizationDetailData();
        $updatedAssetCapitalizationDetail = $this->assetCapitalizationDetailRepo->update($fakeAssetCapitalizationDetail, $assetCapitalizationDetail->id);
        $this->assertModelData($fakeAssetCapitalizationDetail, $updatedAssetCapitalizationDetail->toArray());
        $dbAssetCapitalizationDetail = $this->assetCapitalizationDetailRepo->find($assetCapitalizationDetail->id);
        $this->assertModelData($fakeAssetCapitalizationDetail, $dbAssetCapitalizationDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->makeAssetCapitalizationDetail();
        $resp = $this->assetCapitalizationDetailRepo->delete($assetCapitalizationDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetCapitalizationDetail::find($assetCapitalizationDetail->id), 'AssetCapitalizationDetail should not exist in DB');
    }
}
