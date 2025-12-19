<?php namespace Tests\Repositories;

use App\Models\ProcumentActivity;
use App\Repositories\ProcumentActivityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProcumentActivityRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProcumentActivityRepository
     */
    protected $procumentActivityRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->procumentActivityRepo = \App::make(ProcumentActivityRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->make()->toArray();

        $createdProcumentActivity = $this->procumentActivityRepo->create($procumentActivity);

        $createdProcumentActivity = $createdProcumentActivity->toArray();
        $this->assertArrayHasKey('id', $createdProcumentActivity);
        $this->assertNotNull($createdProcumentActivity['id'], 'Created ProcumentActivity must have id specified');
        $this->assertNotNull(ProcumentActivity::find($createdProcumentActivity['id']), 'ProcumentActivity with given id must be in DB');
        $this->assertModelData($procumentActivity, $createdProcumentActivity);
    }

    /**
     * @test read
     */
    public function test_read_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->create();

        $dbProcumentActivity = $this->procumentActivityRepo->find($procumentActivity->id);

        $dbProcumentActivity = $dbProcumentActivity->toArray();
        $this->assertModelData($procumentActivity->toArray(), $dbProcumentActivity);
    }

    /**
     * @test update
     */
    public function test_update_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->create();
        $fakeProcumentActivity = factory(ProcumentActivity::class)->make()->toArray();

        $updatedProcumentActivity = $this->procumentActivityRepo->update($fakeProcumentActivity, $procumentActivity->id);

        $this->assertModelData($fakeProcumentActivity, $updatedProcumentActivity->toArray());
        $dbProcumentActivity = $this->procumentActivityRepo->find($procumentActivity->id);
        $this->assertModelData($fakeProcumentActivity, $dbProcumentActivity->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->create();

        $resp = $this->procumentActivityRepo->delete($procumentActivity->id);

        $this->assertTrue($resp);
        $this->assertNull(ProcumentActivity::find($procumentActivity->id), 'ProcumentActivity should not exist in DB');
    }
}
