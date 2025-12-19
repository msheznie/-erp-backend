<?php

use App\Models\AssetCapitalization;
use App\Repositories\AssetCapitalizationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizationRepositoryTest extends TestCase
{
    use MakeAssetCapitalizationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetCapitalizationRepository
     */
    protected $assetCapitalizationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetCapitalizationRepo = App::make(AssetCapitalizationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetCapitalization()
    {
        $assetCapitalization = $this->fakeAssetCapitalizationData();
        $createdAssetCapitalization = $this->assetCapitalizationRepo->create($assetCapitalization);
        $createdAssetCapitalization = $createdAssetCapitalization->toArray();
        $this->assertArrayHasKey('id', $createdAssetCapitalization);
        $this->assertNotNull($createdAssetCapitalization['id'], 'Created AssetCapitalization must have id specified');
        $this->assertNotNull(AssetCapitalization::find($createdAssetCapitalization['id']), 'AssetCapitalization with given id must be in DB');
        $this->assertModelData($assetCapitalization, $createdAssetCapitalization);
    }

    /**
     * @test read
     */
    public function testReadAssetCapitalization()
    {
        $assetCapitalization = $this->makeAssetCapitalization();
        $dbAssetCapitalization = $this->assetCapitalizationRepo->find($assetCapitalization->id);
        $dbAssetCapitalization = $dbAssetCapitalization->toArray();
        $this->assertModelData($assetCapitalization->toArray(), $dbAssetCapitalization);
    }

    /**
     * @test update
     */
    public function testUpdateAssetCapitalization()
    {
        $assetCapitalization = $this->makeAssetCapitalization();
        $fakeAssetCapitalization = $this->fakeAssetCapitalizationData();
        $updatedAssetCapitalization = $this->assetCapitalizationRepo->update($fakeAssetCapitalization, $assetCapitalization->id);
        $this->assertModelData($fakeAssetCapitalization, $updatedAssetCapitalization->toArray());
        $dbAssetCapitalization = $this->assetCapitalizationRepo->find($assetCapitalization->id);
        $this->assertModelData($fakeAssetCapitalization, $dbAssetCapitalization->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetCapitalization()
    {
        $assetCapitalization = $this->makeAssetCapitalization();
        $resp = $this->assetCapitalizationRepo->delete($assetCapitalization->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetCapitalization::find($assetCapitalization->id), 'AssetCapitalization should not exist in DB');
    }
}
