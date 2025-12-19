<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMPublicLink;

class SRMPublicLinkApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_public_links', $sRMPublicLink
        );

        $this->assertApiResponse($sRMPublicLink);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_public_links/'.$sRMPublicLink->id
        );

        $this->assertApiResponse($sRMPublicLink->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->create();
        $editedSRMPublicLink = factory(SRMPublicLink::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_public_links/'.$sRMPublicLink->id,
            $editedSRMPublicLink
        );

        $this->assertApiResponse($editedSRMPublicLink);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_public_link()
    {
        $sRMPublicLink = factory(SRMPublicLink::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_public_links/'.$sRMPublicLink->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_public_links/'.$sRMPublicLink->id
        );

        $this->response->assertStatus(404);
    }
}
