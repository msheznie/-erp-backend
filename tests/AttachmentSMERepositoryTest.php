<?php namespace Tests\Repositories;

use App\Models\AttachmentSME;
use App\Repositories\AttachmentSMERepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AttachmentSMERepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttachmentSMERepository
     */
    protected $attachmentSMERepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->attachmentSMERepo = \App::make(AttachmentSMERepository::class);
    }

    /**
     * @test create
     */
    public function test_create_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->make()->toArray();

        $createdAttachmentSME = $this->attachmentSMERepo->create($attachmentSME);

        $createdAttachmentSME = $createdAttachmentSME->toArray();
        $this->assertArrayHasKey('id', $createdAttachmentSME);
        $this->assertNotNull($createdAttachmentSME['id'], 'Created AttachmentSME must have id specified');
        $this->assertNotNull(AttachmentSME::find($createdAttachmentSME['id']), 'AttachmentSME with given id must be in DB');
        $this->assertModelData($attachmentSME, $createdAttachmentSME);
    }

    /**
     * @test read
     */
    public function test_read_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->create();

        $dbAttachmentSME = $this->attachmentSMERepo->find($attachmentSME->id);

        $dbAttachmentSME = $dbAttachmentSME->toArray();
        $this->assertModelData($attachmentSME->toArray(), $dbAttachmentSME);
    }

    /**
     * @test update
     */
    public function test_update_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->create();
        $fakeAttachmentSME = factory(AttachmentSME::class)->make()->toArray();

        $updatedAttachmentSME = $this->attachmentSMERepo->update($fakeAttachmentSME, $attachmentSME->id);

        $this->assertModelData($fakeAttachmentSME, $updatedAttachmentSME->toArray());
        $dbAttachmentSME = $this->attachmentSMERepo->find($attachmentSME->id);
        $this->assertModelData($fakeAttachmentSME, $dbAttachmentSME->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_attachment_s_m_e()
    {
        $attachmentSME = factory(AttachmentSME::class)->create();

        $resp = $this->attachmentSMERepo->delete($attachmentSME->id);

        $this->assertTrue($resp);
        $this->assertNull(AttachmentSME::find($attachmentSME->id), 'AttachmentSME should not exist in DB');
    }
}
