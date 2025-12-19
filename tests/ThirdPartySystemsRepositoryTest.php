<?php namespace Tests\Repositories;

use App\Models\ThirdPartySystems;
use App\Repositories\ThirdPartySystemsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ThirdPartySystemsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ThirdPartySystemsRepository
     */
    protected $thirdPartySystemsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->thirdPartySystemsRepo = \App::make(ThirdPartySystemsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->make()->toArray();

        $createdThirdPartySystems = $this->thirdPartySystemsRepo->create($thirdPartySystems);

        $createdThirdPartySystems = $createdThirdPartySystems->toArray();
        $this->assertArrayHasKey('id', $createdThirdPartySystems);
        $this->assertNotNull($createdThirdPartySystems['id'], 'Created ThirdPartySystems must have id specified');
        $this->assertNotNull(ThirdPartySystems::find($createdThirdPartySystems['id']), 'ThirdPartySystems with given id must be in DB');
        $this->assertModelData($thirdPartySystems, $createdThirdPartySystems);
    }

    /**
     * @test read
     */
    public function test_read_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->create();

        $dbThirdPartySystems = $this->thirdPartySystemsRepo->find($thirdPartySystems->id);

        $dbThirdPartySystems = $dbThirdPartySystems->toArray();
        $this->assertModelData($thirdPartySystems->toArray(), $dbThirdPartySystems);
    }

    /**
     * @test update
     */
    public function test_update_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->create();
        $fakeThirdPartySystems = factory(ThirdPartySystems::class)->make()->toArray();

        $updatedThirdPartySystems = $this->thirdPartySystemsRepo->update($fakeThirdPartySystems, $thirdPartySystems->id);

        $this->assertModelData($fakeThirdPartySystems, $updatedThirdPartySystems->toArray());
        $dbThirdPartySystems = $this->thirdPartySystemsRepo->find($thirdPartySystems->id);
        $this->assertModelData($fakeThirdPartySystems, $dbThirdPartySystems->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->create();

        $resp = $this->thirdPartySystemsRepo->delete($thirdPartySystems->id);

        $this->assertTrue($resp);
        $this->assertNull(ThirdPartySystems::find($thirdPartySystems->id), 'ThirdPartySystems should not exist in DB');
    }
}
