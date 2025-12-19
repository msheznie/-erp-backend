<?php namespace Tests\Repositories;

use App\Models\HRDocumentDescriptionForms;
use App\Repositories\HRDocumentDescriptionFormsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HRDocumentDescriptionFormsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRDocumentDescriptionFormsRepository
     */
    protected $hRDocumentDescriptionFormsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRDocumentDescriptionFormsRepo = \App::make(HRDocumentDescriptionFormsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->make()->toArray();

        $createdHRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepo->create($hRDocumentDescriptionForms);

        $createdHRDocumentDescriptionForms = $createdHRDocumentDescriptionForms->toArray();
        $this->assertArrayHasKey('id', $createdHRDocumentDescriptionForms);
        $this->assertNotNull($createdHRDocumentDescriptionForms['id'], 'Created HRDocumentDescriptionForms must have id specified');
        $this->assertNotNull(HRDocumentDescriptionForms::find($createdHRDocumentDescriptionForms['id']), 'HRDocumentDescriptionForms with given id must be in DB');
        $this->assertModelData($hRDocumentDescriptionForms, $createdHRDocumentDescriptionForms);
    }

    /**
     * @test read
     */
    public function test_read_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->create();

        $dbHRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepo->find($hRDocumentDescriptionForms->id);

        $dbHRDocumentDescriptionForms = $dbHRDocumentDescriptionForms->toArray();
        $this->assertModelData($hRDocumentDescriptionForms->toArray(), $dbHRDocumentDescriptionForms);
    }

    /**
     * @test update
     */
    public function test_update_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->create();
        $fakeHRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->make()->toArray();

        $updatedHRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepo->update($fakeHRDocumentDescriptionForms, $hRDocumentDescriptionForms->id);

        $this->assertModelData($fakeHRDocumentDescriptionForms, $updatedHRDocumentDescriptionForms->toArray());
        $dbHRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepo->find($hRDocumentDescriptionForms->id);
        $this->assertModelData($fakeHRDocumentDescriptionForms, $dbHRDocumentDescriptionForms->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->create();

        $resp = $this->hRDocumentDescriptionFormsRepo->delete($hRDocumentDescriptionForms->id);

        $this->assertTrue($resp);
        $this->assertNull(HRDocumentDescriptionForms::find($hRDocumentDescriptionForms->id), 'HRDocumentDescriptionForms should not exist in DB');
    }
}
