<?php namespace Tests\Repositories;

use App\Models\AssetRequest;
use App\Repositories\AssetRequestRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetRequestRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetRequestRepository
     */
    protected $assetRequestRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetRequestRepo = \App::make(AssetRequestRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->make()->toArray();

        $createdAssetRequest = $this->assetRequestRepo->create($assetRequest);

        $createdAssetRequest = $createdAssetRequest->toArray();
        $this->assertArrayHasKey('id', $createdAssetRequest);
        $this->assertNotNull($createdAssetRequest['id'], 'Created AssetRequest must have id specified');
        $this->assertNotNull(AssetRequest::find($createdAssetRequest['id']), 'AssetRequest with given id must be in DB');
        $this->assertModelData($assetRequest, $createdAssetRequest);
    }

    /**
     * @test read
     */
    public function test_read_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->create();

        $dbAssetRequest = $this->assetRequestRepo->find($assetRequest->id);

        $dbAssetRequest = $dbAssetRequest->toArray();
        $this->assertModelData($assetRequest->toArray(), $dbAssetRequest);
    }

    /**
     * @test update
     */
    public function test_update_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->create();
        $fakeAssetRequest = factory(AssetRequest::class)->make()->toArray();

        $updatedAssetRequest = $this->assetRequestRepo->update($fakeAssetRequest, $assetRequest->id);

        $this->assertModelData($fakeAssetRequest, $updatedAssetRequest->toArray());
        $dbAssetRequest = $this->assetRequestRepo->find($assetRequest->id);
        $this->assertModelData($fakeAssetRequest, $dbAssetRequest->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->create();

        $resp = $this->assetRequestRepo->delete($assetRequest->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetRequest::find($assetRequest->id), 'AssetRequest should not exist in DB');
    }
}
