<?php namespace Tests\Repositories;

use App\Models\AssetVerification;
use App\Repositories\AssetVerificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AssetVerificationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetVerificationRepository
     */
    protected $assetVerificationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->assetVerificationRepo = \App::make(AssetVerificationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->make()->toArray();

        $createdAssetVerification = $this->assetVerificationRepo->create($assetVerification);

        $createdAssetVerification = $createdAssetVerification->toArray();
        $this->assertArrayHasKey('id', $createdAssetVerification);
        $this->assertNotNull($createdAssetVerification['id'], 'Created AssetVerification must have id specified');
        $this->assertNotNull(AssetVerification::find($createdAssetVerification['id']), 'AssetVerification with given id must be in DB');
        $this->assertModelData($assetVerification, $createdAssetVerification);
    }

    /**
     * @test read
     */
    public function test_read_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->create();

        $dbAssetVerification = $this->assetVerificationRepo->find($assetVerification->id);

        $dbAssetVerification = $dbAssetVerification->toArray();
        $this->assertModelData($assetVerification->toArray(), $dbAssetVerification);
    }

    /**
     * @test update
     */
    public function test_update_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->create();
        $fakeAssetVerification = factory(AssetVerification::class)->make()->toArray();

        $updatedAssetVerification = $this->assetVerificationRepo->update($fakeAssetVerification, $assetVerification->id);

        $this->assertModelData($fakeAssetVerification, $updatedAssetVerification->toArray());
        $dbAssetVerification = $this->assetVerificationRepo->find($assetVerification->id);
        $this->assertModelData($fakeAssetVerification, $dbAssetVerification->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->create();

        $resp = $this->assetVerificationRepo->delete($assetVerification->id);

        $this->assertTrue($resp);
        $this->assertNull(AssetVerification::find($assetVerification->id), 'AssetVerification should not exist in DB');
    }
}
