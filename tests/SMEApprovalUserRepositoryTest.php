<?php namespace Tests\Repositories;

use App\Models\SMEApprovalUser;
use App\Repositories\SMEApprovalUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEApprovalUserRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEApprovalUserRepository
     */
    protected $sMEApprovalUserRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEApprovalUserRepo = \App::make(SMEApprovalUserRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->make()->toArray();

        $createdSMEApprovalUser = $this->sMEApprovalUserRepo->create($sMEApprovalUser);

        $createdSMEApprovalUser = $createdSMEApprovalUser->toArray();
        $this->assertArrayHasKey('id', $createdSMEApprovalUser);
        $this->assertNotNull($createdSMEApprovalUser['id'], 'Created SMEApprovalUser must have id specified');
        $this->assertNotNull(SMEApprovalUser::find($createdSMEApprovalUser['id']), 'SMEApprovalUser with given id must be in DB');
        $this->assertModelData($sMEApprovalUser, $createdSMEApprovalUser);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->create();

        $dbSMEApprovalUser = $this->sMEApprovalUserRepo->find($sMEApprovalUser->id);

        $dbSMEApprovalUser = $dbSMEApprovalUser->toArray();
        $this->assertModelData($sMEApprovalUser->toArray(), $dbSMEApprovalUser);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->create();
        $fakeSMEApprovalUser = factory(SMEApprovalUser::class)->make()->toArray();

        $updatedSMEApprovalUser = $this->sMEApprovalUserRepo->update($fakeSMEApprovalUser, $sMEApprovalUser->id);

        $this->assertModelData($fakeSMEApprovalUser, $updatedSMEApprovalUser->toArray());
        $dbSMEApprovalUser = $this->sMEApprovalUserRepo->find($sMEApprovalUser->id);
        $this->assertModelData($fakeSMEApprovalUser, $dbSMEApprovalUser->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->create();

        $resp = $this->sMEApprovalUserRepo->delete($sMEApprovalUser->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEApprovalUser::find($sMEApprovalUser->id), 'SMEApprovalUser should not exist in DB');
    }
}
