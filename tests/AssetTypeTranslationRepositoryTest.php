<?php namespace Tests\Repositories;

use App\Models\AssetTypeTranslation;
use App\Repositories\AssetTypeTranslationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetTypeTranslationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetTypeTranslationRepository
     */
    protected $assetTypeTranslationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetTypeTranslationRepo = \App::make(AssetTypeTranslationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->make()->toArray();

        $createdAssetTypeTranslation = $this->assetTypeTranslationRepo->create($assetTypeTranslation);

        $createdAssetTypeTranslation = $createdAssetTypeTranslation->toArray();
        $this->assertArrayHasKey('id', $createdAssetTypeTranslation);
        $this->assertNotNull($createdAssetTypeTranslation['id'], 'Created AssetTypeTranslation must have id specified');
        $this->assertNotNull(AssetTypeTranslation::find($createdAssetTypeTranslation['id']), 'AssetTypeTranslation with given id must be in DB');
        $this->assertModelData($assetTypeTranslation, $createdAssetTypeTranslation);
    }

    /**
     * @test read
     */
    public function test_read_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->create();

        $dbAssetTypeTranslation = $this->assetTypeTranslationRepo->find($assetTypeTranslation->id);

        $dbAssetTypeTranslation = $dbAssetTypeTranslation->toArray();
        $this->assertModelData($assetTypeTranslation->toArray(), $dbAssetTypeTranslation);
    }

    /**
     * @test update
     */
    public function test_update_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->create();
        $fakeAssetTypeTranslation = factory(AssetTypeTranslation::class)->make()->toArray();

        $updatedAssetTypeTranslation = $this->assetTypeTranslationRepo->update($fakeAssetTypeTranslation, $assetTypeTranslation->id);

        $this->assertModelData($fakeAssetTypeTranslation, $updatedAssetTypeTranslation->toArray());
        $dbAssetTypeTranslation = $this->assetTypeTranslationRepo->find($assetTypeTranslation->id);
        $this->assertModelData($fakeAssetTypeTranslation, $dbAssetTypeTranslation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->create();

        $resp = $this->assetTypeTranslationRepo->delete($assetTypeTranslation->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetTypeTranslation::find($assetTypeTranslation->id), 'AssetTypeTranslation should not exist in DB');
    }
}
