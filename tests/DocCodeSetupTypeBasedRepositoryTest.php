<?php namespace Tests\Repositories;

use App\Models\DocCodeSetupTypeBased;
use App\Repositories\DocCodeSetupTypeBasedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocCodeSetupTypeBasedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocCodeSetupTypeBasedRepository
     */
    protected $docCodeSetupTypeBasedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->docCodeSetupTypeBasedRepo = \App::make(DocCodeSetupTypeBasedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->make()->toArray();

        $createdDocCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepo->create($docCodeSetupTypeBased);

        $createdDocCodeSetupTypeBased = $createdDocCodeSetupTypeBased->toArray();
        $this->assertArrayHasKey('id', $createdDocCodeSetupTypeBased);
        $this->assertNotNull($createdDocCodeSetupTypeBased['id'], 'Created DocCodeSetupTypeBased must have id specified');
        $this->assertNotNull(DocCodeSetupTypeBased::find($createdDocCodeSetupTypeBased['id']), 'DocCodeSetupTypeBased with given id must be in DB');
        $this->assertModelData($docCodeSetupTypeBased, $createdDocCodeSetupTypeBased);
    }

    /**
     * @test read
     */
    public function test_read_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->create();

        $dbDocCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepo->find($docCodeSetupTypeBased->id);

        $dbDocCodeSetupTypeBased = $dbDocCodeSetupTypeBased->toArray();
        $this->assertModelData($docCodeSetupTypeBased->toArray(), $dbDocCodeSetupTypeBased);
    }

    /**
     * @test update
     */
    public function test_update_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->create();
        $fakeDocCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->make()->toArray();

        $updatedDocCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepo->update($fakeDocCodeSetupTypeBased, $docCodeSetupTypeBased->id);

        $this->assertModelData($fakeDocCodeSetupTypeBased, $updatedDocCodeSetupTypeBased->toArray());
        $dbDocCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepo->find($docCodeSetupTypeBased->id);
        $this->assertModelData($fakeDocCodeSetupTypeBased, $dbDocCodeSetupTypeBased->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->create();

        $resp = $this->docCodeSetupTypeBasedRepo->delete($docCodeSetupTypeBased->id);

        $this->assertTrue($resp);
        $this->assertNull(DocCodeSetupTypeBased::find($docCodeSetupTypeBased->id), 'DocCodeSetupTypeBased should not exist in DB');
    }
}
