<?php namespace Tests\Repositories;

use App\Models\SRMTenderUserAccess;
use App\Repositories\SRMTenderUserAccessRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMTenderUserAccessRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMTenderUserAccessRepository
     */
    protected $sRMTenderUserAccessRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMTenderUserAccessRepo = \App::make(SRMTenderUserAccessRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->make()->toArray();

        $createdSRMTenderUserAccess = $this->sRMTenderUserAccessRepo->create($sRMTenderUserAccess);

        $createdSRMTenderUserAccess = $createdSRMTenderUserAccess->toArray();
        $this->assertArrayHasKey('id', $createdSRMTenderUserAccess);
        $this->assertNotNull($createdSRMTenderUserAccess['id'], 'Created SRMTenderUserAccess must have id specified');
        $this->assertNotNull(SRMTenderUserAccess::find($createdSRMTenderUserAccess['id']), 'SRMTenderUserAccess with given id must be in DB');
        $this->assertModelData($sRMTenderUserAccess, $createdSRMTenderUserAccess);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->create();

        $dbSRMTenderUserAccess = $this->sRMTenderUserAccessRepo->find($sRMTenderUserAccess->id);

        $dbSRMTenderUserAccess = $dbSRMTenderUserAccess->toArray();
        $this->assertModelData($sRMTenderUserAccess->toArray(), $dbSRMTenderUserAccess);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->create();
        $fakeSRMTenderUserAccess = factory(SRMTenderUserAccess::class)->make()->toArray();

        $updatedSRMTenderUserAccess = $this->sRMTenderUserAccessRepo->update($fakeSRMTenderUserAccess, $sRMTenderUserAccess->id);

        $this->assertModelData($fakeSRMTenderUserAccess, $updatedSRMTenderUserAccess->toArray());
        $dbSRMTenderUserAccess = $this->sRMTenderUserAccessRepo->find($sRMTenderUserAccess->id);
        $this->assertModelData($fakeSRMTenderUserAccess, $dbSRMTenderUserAccess->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->create();

        $resp = $this->sRMTenderUserAccessRepo->delete($sRMTenderUserAccess->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMTenderUserAccess::find($sRMTenderUserAccess->id), 'SRMTenderUserAccess should not exist in DB');
    }
}
