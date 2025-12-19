<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEApprovalUser;

class SMEApprovalUserApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_approval_users', $sMEApprovalUser
        );

        $this->assertApiResponse($sMEApprovalUser);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_approval_users/'.$sMEApprovalUser->id
        );

        $this->assertApiResponse($sMEApprovalUser->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->create();
        $editedSMEApprovalUser = factory(SMEApprovalUser::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_approval_users/'.$sMEApprovalUser->id,
            $editedSMEApprovalUser
        );

        $this->assertApiResponse($editedSMEApprovalUser);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_approval_user()
    {
        $sMEApprovalUser = factory(SMEApprovalUser::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_approval_users/'.$sMEApprovalUser->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_approval_users/'.$sMEApprovalUser->id
        );

        $this->response->assertStatus(404);
    }
}
