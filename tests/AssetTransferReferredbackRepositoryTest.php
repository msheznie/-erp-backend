<?php namespace Tests\Repositories;

use App\Models\AssetTransferReferredback;
use App\Repositories\AssetTransferReferredbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetTransferReferredbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetTransferReferredbackRepository
     */
    protected $assetTransferReferredbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetTransferReferredbackRepo = \App::make(AssetTransferReferredbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->make()->toArray();

        $createdAssetTransferReferredback = $this->assetTransferReferredbackRepo->create($assetTransferReferredback);

        $createdAssetTransferReferredback = $createdAssetTransferReferredback->toArray();
        $this->assertArrayHasKey('id', $createdAssetTransferReferredback);
        $this->assertNotNull($createdAssetTransferReferredback['id'], 'Created AssetTransferReferredback must have id specified');
        $this->assertNotNull(AssetTransferReferredback::find($createdAssetTransferReferredback['id']), 'AssetTransferReferredback with given id must be in DB');
        $this->assertModelData($assetTransferReferredback, $createdAssetTransferReferredback);
    }

    /**
     * @test read
     */
    public function test_read_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->create();

        $dbAssetTransferReferredback = $this->assetTransferReferredbackRepo->find($assetTransferReferredback->id);

        $dbAssetTransferReferredback = $dbAssetTransferReferredback->toArray();
        $this->assertModelData($assetTransferReferredback->toArray(), $dbAssetTransferReferredback);
    }

    /**
     * @test update
     */
    public function test_update_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->create();
        $fakeAssetTransferReferredback = factory(AssetTransferReferredback::class)->make()->toArray();

        $updatedAssetTransferReferredback = $this->assetTransferReferredbackRepo->update($fakeAssetTransferReferredback, $assetTransferReferredback->id);

        $this->assertModelData($fakeAssetTransferReferredback, $updatedAssetTransferReferredback->toArray());
        $dbAssetTransferReferredback = $this->assetTransferReferredbackRepo->find($assetTransferReferredback->id);
        $this->assertModelData($fakeAssetTransferReferredback, $dbAssetTransferReferredback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->create();

        $resp = $this->assetTransferReferredbackRepo->delete($assetTransferReferredback->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetTransferReferredback::find($assetTransferReferredback->id), 'AssetTransferReferredback should not exist in DB');
    }
}
