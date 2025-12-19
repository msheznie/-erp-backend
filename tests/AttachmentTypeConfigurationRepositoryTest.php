<?php namespace Tests\Repositories;

use App\Models\AttachmentTypeConfiguration;
use App\Repositories\AttachmentTypeConfigurationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AttachmentTypeConfigurationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttachmentTypeConfigurationRepository
     */
    protected $attachmentTypeConfigurationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->attachmentTypeConfigurationRepo = \App::make(AttachmentTypeConfigurationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->make()->toArray();

        $createdAttachmentTypeConfiguration = $this->attachmentTypeConfigurationRepo->create($attachmentTypeConfiguration);

        $createdAttachmentTypeConfiguration = $createdAttachmentTypeConfiguration->toArray();
        $this->assertArrayHasKey('id', $createdAttachmentTypeConfiguration);
        $this->assertNotNull($createdAttachmentTypeConfiguration['id'], 'Created AttachmentTypeConfiguration must have id specified');
        $this->assertNotNull(AttachmentTypeConfiguration::find($createdAttachmentTypeConfiguration['id']), 'AttachmentTypeConfiguration with given id must be in DB');
        $this->assertModelData($attachmentTypeConfiguration, $createdAttachmentTypeConfiguration);
    }

    /**
     * @test read
     */
    public function test_read_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->create();

        $dbAttachmentTypeConfiguration = $this->attachmentTypeConfigurationRepo->find($attachmentTypeConfiguration->id);

        $dbAttachmentTypeConfiguration = $dbAttachmentTypeConfiguration->toArray();
        $this->assertModelData($attachmentTypeConfiguration->toArray(), $dbAttachmentTypeConfiguration);
    }

    /**
     * @test update
     */
    public function test_update_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->create();
        $fakeAttachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->make()->toArray();

        $updatedAttachmentTypeConfiguration = $this->attachmentTypeConfigurationRepo->update($fakeAttachmentTypeConfiguration, $attachmentTypeConfiguration->id);

        $this->assertModelData($fakeAttachmentTypeConfiguration, $updatedAttachmentTypeConfiguration->toArray());
        $dbAttachmentTypeConfiguration = $this->attachmentTypeConfigurationRepo->find($attachmentTypeConfiguration->id);
        $this->assertModelData($fakeAttachmentTypeConfiguration, $dbAttachmentTypeConfiguration->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_attachment_type_configuration()
    {
        $attachmentTypeConfiguration = factory(AttachmentTypeConfiguration::class)->create();

        $resp = $this->attachmentTypeConfigurationRepo->delete($attachmentTypeConfiguration->id);

        $this->assertTrue($resp);
        $this->assertNull(AttachmentTypeConfiguration::find($attachmentTypeConfiguration->id), 'AttachmentTypeConfiguration should not exist in DB');
    }
}
