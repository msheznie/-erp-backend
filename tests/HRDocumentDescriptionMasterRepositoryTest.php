<?php namespace Tests\Repositories;

use App\Models\HRDocumentDescriptionMaster;
use App\Repositories\HRDocumentDescriptionMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HRDocumentDescriptionMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRDocumentDescriptionMasterRepository
     */
    protected $hRDocumentDescriptionMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRDocumentDescriptionMasterRepo = \App::make(HRDocumentDescriptionMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->make()->toArray();

        $createdHRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepo->create($hRDocumentDescriptionMaster);

        $createdHRDocumentDescriptionMaster = $createdHRDocumentDescriptionMaster->toArray();
        $this->assertArrayHasKey('id', $createdHRDocumentDescriptionMaster);
        $this->assertNotNull($createdHRDocumentDescriptionMaster['id'], 'Created HRDocumentDescriptionMaster must have id specified');
        $this->assertNotNull(HRDocumentDescriptionMaster::find($createdHRDocumentDescriptionMaster['id']), 'HRDocumentDescriptionMaster with given id must be in DB');
        $this->assertModelData($hRDocumentDescriptionMaster, $createdHRDocumentDescriptionMaster);
    }

    /**
     * @test read
     */
    public function test_read_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->create();

        $dbHRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepo->find($hRDocumentDescriptionMaster->id);

        $dbHRDocumentDescriptionMaster = $dbHRDocumentDescriptionMaster->toArray();
        $this->assertModelData($hRDocumentDescriptionMaster->toArray(), $dbHRDocumentDescriptionMaster);
    }

    /**
     * @test update
     */
    public function test_update_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->create();
        $fakeHRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->make()->toArray();

        $updatedHRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepo->update($fakeHRDocumentDescriptionMaster, $hRDocumentDescriptionMaster->id);

        $this->assertModelData($fakeHRDocumentDescriptionMaster, $updatedHRDocumentDescriptionMaster->toArray());
        $dbHRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepo->find($hRDocumentDescriptionMaster->id);
        $this->assertModelData($fakeHRDocumentDescriptionMaster, $dbHRDocumentDescriptionMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->create();

        $resp = $this->hRDocumentDescriptionMasterRepo->delete($hRDocumentDescriptionMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(HRDocumentDescriptionMaster::find($hRDocumentDescriptionMaster->id), 'HRDocumentDescriptionMaster should not exist in DB');
    }
}
