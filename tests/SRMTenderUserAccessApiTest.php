<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMTenderUserAccess;

class SRMTenderUserAccessApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_tender_user_accesses', $sRMTenderUserAccess
        );

        $this->assertApiResponse($sRMTenderUserAccess);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_tender_user_accesses/'.$sRMTenderUserAccess->id
        );

        $this->assertApiResponse($sRMTenderUserAccess->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->create();
        $editedSRMTenderUserAccess = factory(SRMTenderUserAccess::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_tender_user_accesses/'.$sRMTenderUserAccess->id,
            $editedSRMTenderUserAccess
        );

        $this->assertApiResponse($editedSRMTenderUserAccess);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_tender_user_access()
    {
        $sRMTenderUserAccess = factory(SRMTenderUserAccess::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_tender_user_accesses/'.$sRMTenderUserAccess->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_tender_user_accesses/'.$sRMTenderUserAccess->id
        );

        $this->response->assertStatus(404);
    }
}
