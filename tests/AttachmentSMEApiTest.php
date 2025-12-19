<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AttachmentSME;

class AttachmentSMEApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/attachment_s_m_es', $attachmentSME
        );

        $this->assertApiResponse($attachmentSME);
    }

    /**
     * @test
     */
    public function test_read_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/attachment_s_m_es/'.$attachmentSME->id
        );

        $this->assertApiResponse($attachmentSME->toArray());
    }

    /**
     * @test
     */
    public function test_update_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->create();
        $editedAttachmentSME = factory(AttachmentSME::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/attachment_s_m_es/'.$attachmentSME->id,
            $editedAttachmentSME
        );

        $this->assertApiResponse($editedAttachmentSME);
    }

    /**
     * @test
     */
    public function test_delete_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/attachment_s_m_es/'.$attachmentSME->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/attachment_s_m_es/'.$attachmentSME->id
        );

        $this->response->assertStatus(404);
    }
}
