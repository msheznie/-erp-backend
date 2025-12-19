<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEOertimeGroupMaster;

class SMEOertimeGroupMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_oertime_group_masters', $sMEOertimeGroupMaster
        );

        $this->assertApiResponse($sMEOertimeGroupMaster);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_oertime_group_masters/'.$sMEOertimeGroupMaster->id
        );

        $this->assertApiResponse($sMEOertimeGroupMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->create();
        $editedSMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_oertime_group_masters/'.$sMEOertimeGroupMaster->id,
            $editedSMEOertimeGroupMaster
        );

        $this->assertApiResponse($editedSMEOertimeGroupMaster);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_oertime_group_masters/'.$sMEOertimeGroupMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_oertime_group_masters/'.$sMEOertimeGroupMaster->id
        );

        $this->response->assertStatus(404);
    }
}
