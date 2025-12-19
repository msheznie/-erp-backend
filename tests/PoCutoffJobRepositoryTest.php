<?php namespace Tests\Repositories;

use App\Models\PoCutoffJob;
use App\Repositories\PoCutoffJobRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PoCutoffJobRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoCutoffJobRepository
     */
    protected $poCutoffJobRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->poCutoffJobRepo = \App::make(PoCutoffJobRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->make()->toArray();

        $createdPoCutoffJob = $this->poCutoffJobRepo->create($poCutoffJob);

        $createdPoCutoffJob = $createdPoCutoffJob->toArray();
        $this->assertArrayHasKey('id', $createdPoCutoffJob);
        $this->assertNotNull($createdPoCutoffJob['id'], 'Created PoCutoffJob must have id specified');
        $this->assertNotNull(PoCutoffJob::find($createdPoCutoffJob['id']), 'PoCutoffJob with given id must be in DB');
        $this->assertModelData($poCutoffJob, $createdPoCutoffJob);
    }

    /**
     * @test read
     */
    public function test_read_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->create();

        $dbPoCutoffJob = $this->poCutoffJobRepo->find($poCutoffJob->id);

        $dbPoCutoffJob = $dbPoCutoffJob->toArray();
        $this->assertModelData($poCutoffJob->toArray(), $dbPoCutoffJob);
    }

    /**
     * @test update
     */
    public function test_update_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->create();
        $fakePoCutoffJob = factory(PoCutoffJob::class)->make()->toArray();

        $updatedPoCutoffJob = $this->poCutoffJobRepo->update($fakePoCutoffJob, $poCutoffJob->id);

        $this->assertModelData($fakePoCutoffJob, $updatedPoCutoffJob->toArray());
        $dbPoCutoffJob = $this->poCutoffJobRepo->find($poCutoffJob->id);
        $this->assertModelData($fakePoCutoffJob, $dbPoCutoffJob->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->create();

        $resp = $this->poCutoffJobRepo->delete($poCutoffJob->id);

        $this->assertTrue($resp);
        $this->assertNull(PoCutoffJob::find($poCutoffJob->id), 'PoCutoffJob should not exist in DB');
    }
}
