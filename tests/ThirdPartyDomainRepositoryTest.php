<?php namespace Tests\Repositories;

use App\Models\ThirdPartyDomain;
use App\Repositories\ThirdPartyDomainRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ThirdPartyDomainRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ThirdPartyDomainRepository
     */
    protected $thirdPartyDomainRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->thirdPartyDomainRepo = \App::make(ThirdPartyDomainRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->make()->toArray();

        $createdThirdPartyDomain = $this->thirdPartyDomainRepo->create($thirdPartyDomain);

        $createdThirdPartyDomain = $createdThirdPartyDomain->toArray();
        $this->assertArrayHasKey('id', $createdThirdPartyDomain);
        $this->assertNotNull($createdThirdPartyDomain['id'], 'Created ThirdPartyDomain must have id specified');
        $this->assertNotNull(ThirdPartyDomain::find($createdThirdPartyDomain['id']), 'ThirdPartyDomain with given id must be in DB');
        $this->assertModelData($thirdPartyDomain, $createdThirdPartyDomain);
    }

    /**
     * @test read
     */
    public function test_read_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->create();

        $dbThirdPartyDomain = $this->thirdPartyDomainRepo->find($thirdPartyDomain->id);

        $dbThirdPartyDomain = $dbThirdPartyDomain->toArray();
        $this->assertModelData($thirdPartyDomain->toArray(), $dbThirdPartyDomain);
    }

    /**
     * @test update
     */
    public function test_update_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->create();
        $fakeThirdPartyDomain = factory(ThirdPartyDomain::class)->make()->toArray();

        $updatedThirdPartyDomain = $this->thirdPartyDomainRepo->update($fakeThirdPartyDomain, $thirdPartyDomain->id);

        $this->assertModelData($fakeThirdPartyDomain, $updatedThirdPartyDomain->toArray());
        $dbThirdPartyDomain = $this->thirdPartyDomainRepo->find($thirdPartyDomain->id);
        $this->assertModelData($fakeThirdPartyDomain, $dbThirdPartyDomain->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->create();

        $resp = $this->thirdPartyDomainRepo->delete($thirdPartyDomain->id);

        $this->assertTrue($resp);
        $this->assertNull(ThirdPartyDomain::find($thirdPartyDomain->id), 'ThirdPartyDomain should not exist in DB');
    }
}
