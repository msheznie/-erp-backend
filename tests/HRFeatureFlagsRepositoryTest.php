<?php namespace Tests\Repositories;

use App\Models\HRFeatureFlags;
use App\Repositories\HRFeatureFlagsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HRFeatureFlagsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRFeatureFlagsRepository
     */
    protected $hRFeatureFlagsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRFeatureFlagsRepo = \App::make(HRFeatureFlagsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->make()->toArray();

        $createdHRFeatureFlags = $this->hRFeatureFlagsRepo->create($hRFeatureFlags);

        $createdHRFeatureFlags = $createdHRFeatureFlags->toArray();
        $this->assertArrayHasKey('id', $createdHRFeatureFlags);
        $this->assertNotNull($createdHRFeatureFlags['id'], 'Created HRFeatureFlags must have id specified');
        $this->assertNotNull(HRFeatureFlags::find($createdHRFeatureFlags['id']), 'HRFeatureFlags with given id must be in DB');
        $this->assertModelData($hRFeatureFlags, $createdHRFeatureFlags);
    }

    /**
     * @test read
     */
    public function test_read_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->create();

        $dbHRFeatureFlags = $this->hRFeatureFlagsRepo->find($hRFeatureFlags->id);

        $dbHRFeatureFlags = $dbHRFeatureFlags->toArray();
        $this->assertModelData($hRFeatureFlags->toArray(), $dbHRFeatureFlags);
    }

    /**
     * @test update
     */
    public function test_update_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->create();
        $fakeHRFeatureFlags = factory(HRFeatureFlags::class)->make()->toArray();

        $updatedHRFeatureFlags = $this->hRFeatureFlagsRepo->update($fakeHRFeatureFlags, $hRFeatureFlags->id);

        $this->assertModelData($fakeHRFeatureFlags, $updatedHRFeatureFlags->toArray());
        $dbHRFeatureFlags = $this->hRFeatureFlagsRepo->find($hRFeatureFlags->id);
        $this->assertModelData($fakeHRFeatureFlags, $dbHRFeatureFlags->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->create();

        $resp = $this->hRFeatureFlagsRepo->delete($hRFeatureFlags->id);

        $this->assertTrue($resp);
        $this->assertNull(HRFeatureFlags::find($hRFeatureFlags->id), 'HRFeatureFlags should not exist in DB');
    }
}
