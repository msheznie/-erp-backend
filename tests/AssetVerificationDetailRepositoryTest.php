<?php namespace Tests\Repositories;

use App\Models\AssetVerificationDetail;
use App\Repositories\AssetVerificationDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetVerificationDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetVerificationDetailRepository
     */
    protected $assetVerificationDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetVerificationDetailRepo = \App::make(AssetVerificationDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->make()->toArray();

        $createdAssetVerificationDetail = $this->assetVerificationDetailRepo->create($assetVerificationDetail);

        $createdAssetVerificationDetail = $createdAssetVerificationDetail->toArray();
        $this->assertArrayHasKey('id', $createdAssetVerificationDetail);
        $this->assertNotNull($createdAssetVerificationDetail['id'], 'Created AssetVerificationDetail must have id specified');
        $this->assertNotNull(AssetVerificationDetail::find($createdAssetVerificationDetail['id']), 'AssetVerificationDetail with given id must be in DB');
        $this->assertModelData($assetVerificationDetail, $createdAssetVerificationDetail);
    }

    /**
     * @test read
     */
    public function test_read_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->create();

        $dbAssetVerificationDetail = $this->assetVerificationDetailRepo->find($assetVerificationDetail->id);

        $dbAssetVerificationDetail = $dbAssetVerificationDetail->toArray();
        $this->assertModelData($assetVerificationDetail->toArray(), $dbAssetVerificationDetail);
    }

    /**
     * @test update
     */
    public function test_update_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->create();
        $fakeAssetVerificationDetail = factory(AssetVerificationDetail::class)->make()->toArray();

        $updatedAssetVerificationDetail = $this->assetVerificationDetailRepo->update($fakeAssetVerificationDetail, $assetVerificationDetail->id);

        $this->assertModelData($fakeAssetVerificationDetail, $updatedAssetVerificationDetail->toArray());
        $dbAssetVerificationDetail = $this->assetVerificationDetailRepo->find($assetVerificationDetail->id);
        $this->assertModelData($fakeAssetVerificationDetail, $dbAssetVerificationDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->create();

        $resp = $this->assetVerificationDetailRepo->delete($assetVerificationDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetVerificationDetail::find($assetVerificationDetail->id), 'AssetVerificationDetail should not exist in DB');
    }
}
