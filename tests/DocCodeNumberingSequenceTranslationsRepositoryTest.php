<?php namespace Tests\Repositories;

use App\Models\DocCodeNumberingSequenceTranslations;
use App\Repositories\DocCodeNumberingSequenceTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocCodeNumberingSequenceTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocCodeNumberingSequenceTranslationsRepository
     */
    protected $docCodeNumberingSequenceTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->docCodeNumberingSequenceTranslationsRepo = \App::make(DocCodeNumberingSequenceTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->make()->toArray();

        $createdDocCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepo->create($docCodeNumberingSequenceTranslations);

        $createdDocCodeNumberingSequenceTranslations = $createdDocCodeNumberingSequenceTranslations->toArray();
        $this->assertArrayHasKey('id', $createdDocCodeNumberingSequenceTranslations);
        $this->assertNotNull($createdDocCodeNumberingSequenceTranslations['id'], 'Created DocCodeNumberingSequenceTranslations must have id specified');
        $this->assertNotNull(DocCodeNumberingSequenceTranslations::find($createdDocCodeNumberingSequenceTranslations['id']), 'DocCodeNumberingSequenceTranslations with given id must be in DB');
        $this->assertModelData($docCodeNumberingSequenceTranslations, $createdDocCodeNumberingSequenceTranslations);
    }

    /**
     * @test read
     */
    public function test_read_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->create();

        $dbDocCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepo->find($docCodeNumberingSequenceTranslations->id);

        $dbDocCodeNumberingSequenceTranslations = $dbDocCodeNumberingSequenceTranslations->toArray();
        $this->assertModelData($docCodeNumberingSequenceTranslations->toArray(), $dbDocCodeNumberingSequenceTranslations);
    }

    /**
     * @test update
     */
    public function test_update_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->create();
        $fakeDocCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->make()->toArray();

        $updatedDocCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepo->update($fakeDocCodeNumberingSequenceTranslations, $docCodeNumberingSequenceTranslations->id);

        $this->assertModelData($fakeDocCodeNumberingSequenceTranslations, $updatedDocCodeNumberingSequenceTranslations->toArray());
        $dbDocCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepo->find($docCodeNumberingSequenceTranslations->id);
        $this->assertModelData($fakeDocCodeNumberingSequenceTranslations, $dbDocCodeNumberingSequenceTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->create();

        $resp = $this->docCodeNumberingSequenceTranslationsRepo->delete($docCodeNumberingSequenceTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(DocCodeNumberingSequenceTranslations::find($docCodeNumberingSequenceTranslations->id), 'DocCodeNumberingSequenceTranslations should not exist in DB');
    }
}
