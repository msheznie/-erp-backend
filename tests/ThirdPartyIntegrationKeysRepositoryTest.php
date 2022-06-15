<?php namespace Tests\Repositories;

use App\Models\ThirdPartyIntegrationKeys;
use App\Repositories\ThirdPartyIntegrationKeysRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ThirdPartyIntegrationKeysRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ThirdPartyIntegrationKeysRepository
     */
    protected $thirdPartyIntegrationKeysRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->thirdPartyIntegrationKeysRepo = \App::make(ThirdPartyIntegrationKeysRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->make()->toArray();

        $createdThirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepo->create($thirdPartyIntegrationKeys);

        $createdThirdPartyIntegrationKeys = $createdThirdPartyIntegrationKeys->toArray();
        $this->assertArrayHasKey('id', $createdThirdPartyIntegrationKeys);
        $this->assertNotNull($createdThirdPartyIntegrationKeys['id'], 'Created ThirdPartyIntegrationKeys must have id specified');
        $this->assertNotNull(ThirdPartyIntegrationKeys::find($createdThirdPartyIntegrationKeys['id']), 'ThirdPartyIntegrationKeys with given id must be in DB');
        $this->assertModelData($thirdPartyIntegrationKeys, $createdThirdPartyIntegrationKeys);
    }

    /**
     * @test read
     */
    public function test_read_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->create();

        $dbThirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepo->find($thirdPartyIntegrationKeys->id);

        $dbThirdPartyIntegrationKeys = $dbThirdPartyIntegrationKeys->toArray();
        $this->assertModelData($thirdPartyIntegrationKeys->toArray(), $dbThirdPartyIntegrationKeys);
    }

    /**
     * @test update
     */
    public function test_update_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->create();
        $fakeThirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->make()->toArray();

        $updatedThirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepo->update($fakeThirdPartyIntegrationKeys, $thirdPartyIntegrationKeys->id);

        $this->assertModelData($fakeThirdPartyIntegrationKeys, $updatedThirdPartyIntegrationKeys->toArray());
        $dbThirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepo->find($thirdPartyIntegrationKeys->id);
        $this->assertModelData($fakeThirdPartyIntegrationKeys, $dbThirdPartyIntegrationKeys->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->create();

        $resp = $this->thirdPartyIntegrationKeysRepo->delete($thirdPartyIntegrationKeys->id);

        $this->assertTrue($resp);
        $this->assertNull(ThirdPartyIntegrationKeys::find($thirdPartyIntegrationKeys->id), 'ThirdPartyIntegrationKeys should not exist in DB');
    }
}
