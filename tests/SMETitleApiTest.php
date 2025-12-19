<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMETitle;

class SMETitleApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_titles', $sMETitle
        );

        $this->assertApiResponse($sMETitle);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_titles/'.$sMETitle->id
        );

        $this->assertApiResponse($sMETitle->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->create();
        $editedSMETitle = factory(SMETitle::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_titles/'.$sMETitle->id,
            $editedSMETitle
        );

        $this->assertApiResponse($editedSMETitle);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_titles/'.$sMETitle->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_titles/'.$sMETitle->id
        );

        $this->response->assertStatus(404);
    }
}
