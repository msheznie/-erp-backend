<?php namespace Tests\Repositories;

use App\Models\DocCodeNumberingSequence;
use App\Repositories\DocCodeNumberingSequenceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocCodeNumberingSequenceRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocCodeNumberingSequenceRepository
     */
    protected $docCodeNumberingSequenceRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->docCodeNumberingSequenceRepo = \App::make(DocCodeNumberingSequenceRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->make()->toArray();

        $createdDocCodeNumberingSequence = $this->docCodeNumberingSequenceRepo->create($docCodeNumberingSequence);

        $createdDocCodeNumberingSequence = $createdDocCodeNumberingSequence->toArray();
        $this->assertArrayHasKey('id', $createdDocCodeNumberingSequence);
        $this->assertNotNull($createdDocCodeNumberingSequence['id'], 'Created DocCodeNumberingSequence must have id specified');
        $this->assertNotNull(DocCodeNumberingSequence::find($createdDocCodeNumberingSequence['id']), 'DocCodeNumberingSequence with given id must be in DB');
        $this->assertModelData($docCodeNumberingSequence, $createdDocCodeNumberingSequence);
    }

    /**
     * @test read
     */
    public function test_read_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->create();

        $dbDocCodeNumberingSequence = $this->docCodeNumberingSequenceRepo->find($docCodeNumberingSequence->id);

        $dbDocCodeNumberingSequence = $dbDocCodeNumberingSequence->toArray();
        $this->assertModelData($docCodeNumberingSequence->toArray(), $dbDocCodeNumberingSequence);
    }

    /**
     * @test update
     */
    public function test_update_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->create();
        $fakeDocCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->make()->toArray();

        $updatedDocCodeNumberingSequence = $this->docCodeNumberingSequenceRepo->update($fakeDocCodeNumberingSequence, $docCodeNumberingSequence->id);

        $this->assertModelData($fakeDocCodeNumberingSequence, $updatedDocCodeNumberingSequence->toArray());
        $dbDocCodeNumberingSequence = $this->docCodeNumberingSequenceRepo->find($docCodeNumberingSequence->id);
        $this->assertModelData($fakeDocCodeNumberingSequence, $dbDocCodeNumberingSequence->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->create();

        $resp = $this->docCodeNumberingSequenceRepo->delete($docCodeNumberingSequence->id);

        $this->assertTrue($resp);
        $this->assertNull(DocCodeNumberingSequence::find($docCodeNumberingSequence->id), 'DocCodeNumberingSequence should not exist in DB');
    }
}
