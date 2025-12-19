<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMELaveGroup;

class SMELaveGroupApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_lave_groups', $sMELaveGroup
        );

        $this->assertApiResponse($sMELaveGroup);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_lave_groups/'.$sMELaveGroup->id
        );

        $this->assertApiResponse($sMELaveGroup->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->create();
        $editedSMELaveGroup = factory(SMELaveGroup::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_lave_groups/'.$sMELaveGroup->id,
            $editedSMELaveGroup
        );

        $this->assertApiResponse($editedSMELaveGroup);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_lave_groups/'.$sMELaveGroup->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_lave_groups/'.$sMELaveGroup->id
        );

        $this->response->assertStatus(404);
    }
}
