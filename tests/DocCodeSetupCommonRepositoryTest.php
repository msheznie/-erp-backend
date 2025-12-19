<?php namespace Tests\Repositories;

use App\Models\DocCodeSetupCommon;
use App\Repositories\DocCodeSetupCommonRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocCodeSetupCommonRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocCodeSetupCommonRepository
     */
    protected $docCodeSetupCommonRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->docCodeSetupCommonRepo = \App::make(DocCodeSetupCommonRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->make()->toArray();

        $createdDocCodeSetupCommon = $this->docCodeSetupCommonRepo->create($docCodeSetupCommon);

        $createdDocCodeSetupCommon = $createdDocCodeSetupCommon->toArray();
        $this->assertArrayHasKey('id', $createdDocCodeSetupCommon);
        $this->assertNotNull($createdDocCodeSetupCommon['id'], 'Created DocCodeSetupCommon must have id specified');
        $this->assertNotNull(DocCodeSetupCommon::find($createdDocCodeSetupCommon['id']), 'DocCodeSetupCommon with given id must be in DB');
        $this->assertModelData($docCodeSetupCommon, $createdDocCodeSetupCommon);
    }

    /**
     * @test read
     */
    public function test_read_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->create();

        $dbDocCodeSetupCommon = $this->docCodeSetupCommonRepo->find($docCodeSetupCommon->id);

        $dbDocCodeSetupCommon = $dbDocCodeSetupCommon->toArray();
        $this->assertModelData($docCodeSetupCommon->toArray(), $dbDocCodeSetupCommon);
    }

    /**
     * @test update
     */
    public function test_update_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->create();
        $fakeDocCodeSetupCommon = factory(DocCodeSetupCommon::class)->make()->toArray();

        $updatedDocCodeSetupCommon = $this->docCodeSetupCommonRepo->update($fakeDocCodeSetupCommon, $docCodeSetupCommon->id);

        $this->assertModelData($fakeDocCodeSetupCommon, $updatedDocCodeSetupCommon->toArray());
        $dbDocCodeSetupCommon = $this->docCodeSetupCommonRepo->find($docCodeSetupCommon->id);
        $this->assertModelData($fakeDocCodeSetupCommon, $dbDocCodeSetupCommon->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->create();

        $resp = $this->docCodeSetupCommonRepo->delete($docCodeSetupCommon->id);

        $this->assertTrue($resp);
        $this->assertNull(DocCodeSetupCommon::find($docCodeSetupCommon->id), 'DocCodeSetupCommon should not exist in DB');
    }
}
