<?php namespace Tests\Repositories;

use App\Models\SRMPublicLink;
use App\Repositories\SRMPublicLinkRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMPublicLinkRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMPublicLinkRepository
     */
    protected $sRMPublicLinkRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMPublicLinkRepo = \App::make(SRMPublicLinkRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->make()->toArray();

        $createdSRMPublicLink = $this->sRMPublicLinkRepo->create($sRMPublicLink);

        $createdSRMPublicLink = $createdSRMPublicLink->toArray();
        $this->assertArrayHasKey('id', $createdSRMPublicLink);
        $this->assertNotNull($createdSRMPublicLink['id'], 'Created SRMPublicLink must have id specified');
        $this->assertNotNull(SRMPublicLink::find($createdSRMPublicLink['id']), 'SRMPublicLink with given id must be in DB');
        $this->assertModelData($sRMPublicLink, $createdSRMPublicLink);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->create();

        $dbSRMPublicLink = $this->sRMPublicLinkRepo->find($sRMPublicLink->id);

        $dbSRMPublicLink = $dbSRMPublicLink->toArray();
        $this->assertModelData($sRMPublicLink->toArray(), $dbSRMPublicLink);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->create();
        $fakeSRMPublicLink = factory(SRMPublicLink::class)->make()->toArray();

        $updatedSRMPublicLink = $this->sRMPublicLinkRepo->update($fakeSRMPublicLink, $sRMPublicLink->id);

        $this->assertModelData($fakeSRMPublicLink, $updatedSRMPublicLink->toArray());
        $dbSRMPublicLink = $this->sRMPublicLinkRepo->find($sRMPublicLink->id);
        $this->assertModelData($fakeSRMPublicLink, $dbSRMPublicLink->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->create();

        $resp = $this->sRMPublicLinkRepo->delete($sRMPublicLink->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMPublicLink::find($sRMPublicLink->id), 'SRMPublicLink should not exist in DB');
    }
}
