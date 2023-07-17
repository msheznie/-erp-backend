<?php namespace Tests\Repositories;

use App\Models\AssetWarranty;
use App\Repositories\AssetWarrantyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetWarrantyRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetWarrantyRepository
     */
    protected $assetWarrantyRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetWarrantyRepo = \App::make(AssetWarrantyRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->make()->toArray();

        $createdAssetWarranty = $this->assetWarrantyRepo->create($assetWarranty);

        $createdAssetWarranty = $createdAssetWarranty->toArray();
        $this->assertArrayHasKey('id', $createdAssetWarranty);
        $this->assertNotNull($createdAssetWarranty['id'], 'Created AssetWarranty must have id specified');
        $this->assertNotNull(AssetWarranty::find($createdAssetWarranty['id']), 'AssetWarranty with given id must be in DB');
        $this->assertModelData($assetWarranty, $createdAssetWarranty);
    }

    /**
     * @test read
     */
    public function test_read_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->create();

        $dbAssetWarranty = $this->assetWarrantyRepo->find($assetWarranty->id);

        $dbAssetWarranty = $dbAssetWarranty->toArray();
        $this->assertModelData($assetWarranty->toArray(), $dbAssetWarranty);
    }

    /**
     * @test update
     */
    public function test_update_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->create();
        $fakeAssetWarranty = factory(AssetWarranty::class)->make()->toArray();

        $updatedAssetWarranty = $this->assetWarrantyRepo->update($fakeAssetWarranty, $assetWarranty->id);

        $this->assertModelData($fakeAssetWarranty, $updatedAssetWarranty->toArray());
        $dbAssetWarranty = $this->assetWarrantyRepo->find($assetWarranty->id);
        $this->assertModelData($fakeAssetWarranty, $dbAssetWarranty->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->create();

        $resp = $this->assetWarrantyRepo->delete($assetWarranty->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetWarranty::find($assetWarranty->id), 'AssetWarranty should not exist in DB');
    }
}
