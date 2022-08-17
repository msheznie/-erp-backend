<?php namespace Tests\Repositories;

use App\Models\PoCutoffJobData;
use App\Repositories\PoCutoffJobDataRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PoCutoffJobDataRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoCutoffJobDataRepository
     */
    protected $poCutoffJobDataRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->poCutoffJobDataRepo = \App::make(PoCutoffJobDataRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->make()->toArray();

        $createdPoCutoffJobData = $this->poCutoffJobDataRepo->create($poCutoffJobData);

        $createdPoCutoffJobData = $createdPoCutoffJobData->toArray();
        $this->assertArrayHasKey('id', $createdPoCutoffJobData);
        $this->assertNotNull($createdPoCutoffJobData['id'], 'Created PoCutoffJobData must have id specified');
        $this->assertNotNull(PoCutoffJobData::find($createdPoCutoffJobData['id']), 'PoCutoffJobData with given id must be in DB');
        $this->assertModelData($poCutoffJobData, $createdPoCutoffJobData);
    }

    /**
     * @test read
     */
    public function test_read_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->create();

        $dbPoCutoffJobData = $this->poCutoffJobDataRepo->find($poCutoffJobData->id);

        $dbPoCutoffJobData = $dbPoCutoffJobData->toArray();
        $this->assertModelData($poCutoffJobData->toArray(), $dbPoCutoffJobData);
    }

    /**
     * @test update
     */
    public function test_update_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->create();
        $fakePoCutoffJobData = factory(PoCutoffJobData::class)->make()->toArray();

        $updatedPoCutoffJobData = $this->poCutoffJobDataRepo->update($fakePoCutoffJobData, $poCutoffJobData->id);

        $this->assertModelData($fakePoCutoffJobData, $updatedPoCutoffJobData->toArray());
        $dbPoCutoffJobData = $this->poCutoffJobDataRepo->find($poCutoffJobData->id);
        $this->assertModelData($fakePoCutoffJobData, $dbPoCutoffJobData->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->create();

        $resp = $this->poCutoffJobDataRepo->delete($poCutoffJobData->id);

        $this->assertTrue($resp);
        $this->assertNull(PoCutoffJobData::find($poCutoffJobData->id), 'PoCutoffJobData should not exist in DB');
    }
}
