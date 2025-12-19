<?php namespace Tests\Repositories;

use App\Models\AssetRequestDetail;
use App\Repositories\AssetRequestDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetRequestDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetRequestDetailRepository
     */
    protected $assetRequestDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetRequestDetailRepo = \App::make(AssetRequestDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->make()->toArray();

        $createdAssetRequestDetail = $this->assetRequestDetailRepo->create($assetRequestDetail);

        $createdAssetRequestDetail = $createdAssetRequestDetail->toArray();
        $this->assertArrayHasKey('id', $createdAssetRequestDetail);
        $this->assertNotNull($createdAssetRequestDetail['id'], 'Created AssetRequestDetail must have id specified');
        $this->assertNotNull(AssetRequestDetail::find($createdAssetRequestDetail['id']), 'AssetRequestDetail with given id must be in DB');
        $this->assertModelData($assetRequestDetail, $createdAssetRequestDetail);
    }

    /**
     * @test read
     */
    public function test_read_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->create();

        $dbAssetRequestDetail = $this->assetRequestDetailRepo->find($assetRequestDetail->id);

        $dbAssetRequestDetail = $dbAssetRequestDetail->toArray();
        $this->assertModelData($assetRequestDetail->toArray(), $dbAssetRequestDetail);
    }

    /**
     * @test update
     */
    public function test_update_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->create();
        $fakeAssetRequestDetail = factory(AssetRequestDetail::class)->make()->toArray();

        $updatedAssetRequestDetail = $this->assetRequestDetailRepo->update($fakeAssetRequestDetail, $assetRequestDetail->id);

        $this->assertModelData($fakeAssetRequestDetail, $updatedAssetRequestDetail->toArray());
        $dbAssetRequestDetail = $this->assetRequestDetailRepo->find($assetRequestDetail->id);
        $this->assertModelData($fakeAssetRequestDetail, $dbAssetRequestDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->create();

        $resp = $this->assetRequestDetailRepo->delete($assetRequestDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetRequestDetail::find($assetRequestDetail->id), 'AssetRequestDetail should not exist in DB');
    }
}
