<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AttachmentTypeConfiguration;

class AttachmentTypeConfigurationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/attachment_type_configurations', $attachmentTypeConfiguration
        );

        $this->assertApiResponse($attachmentTypeConfiguration);
    }

    /**
     * @test
     */
    public function test_read_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/attachment_type_configurations/'.$attachmentTypeConfiguration->id
        );

        $this->assertApiResponse($attachmentTypeConfiguration->toArray());
    }

    /**
     * @test
     */
    public function test_update_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->create();
        $editedAttachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/attachment_type_configurations/'.$attachmentTypeConfiguration->id,
            $editedAttachmentTypeConfiguration
        );

        $this->assertApiResponse($editedAttachmentTypeConfiguration);
    }

    /**
     * @test
     */
    public function test_delete_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/attachment_type_configurations/'.$attachmentTypeConfiguration->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/attachment_type_configurations/'.$attachmentTypeConfiguration->id
        );

        $this->response->assertStatus(404);
    }
}
