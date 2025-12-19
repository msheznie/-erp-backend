<?php namespace Tests\Repositories;

use App\Models\SystemJobs;
use App\Repositories\SystemJobsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SystemJobsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SystemJobsRepository
     */
    protected $systemJobsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->systemJobsRepo = \App::make(SystemJobsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->make()->toArray();

        $createdSystemJobs = $this->systemJobsRepo->create($systemJobs);

        $createdSystemJobs = $createdSystemJobs->toArray();
        $this->assertArrayHasKey('id', $createdSystemJobs);
        $this->assertNotNull($createdSystemJobs['id'], 'Created SystemJobs must have id specified');
        $this->assertNotNull(SystemJobs::find($createdSystemJobs['id']), 'SystemJobs with given id must be in DB');
        $this->assertModelData($systemJobs, $createdSystemJobs);
    }

    /**
     * @test read
     */
    public function test_read_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->create();

        $dbSystemJobs = $this->systemJobsRepo->find($systemJobs->id);

        $dbSystemJobs = $dbSystemJobs->toArray();
        $this->assertModelData($systemJobs->toArray(), $dbSystemJobs);
    }

    /**
     * @test update
     */
    public function test_update_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->create();
        $fakeSystemJobs = factory(SystemJobs::class)->make()->toArray();

        $updatedSystemJobs = $this->systemJobsRepo->update($fakeSystemJobs, $systemJobs->id);

        $this->assertModelData($fakeSystemJobs, $updatedSystemJobs->toArray());
        $dbSystemJobs = $this->systemJobsRepo->find($systemJobs->id);
        $this->assertModelData($fakeSystemJobs, $dbSystemJobs->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->create();

        $resp = $this->systemJobsRepo->delete($systemJobs->id);

        $this->assertTrue($resp);
        $this->assertNull(SystemJobs::find($systemJobs->id), 'SystemJobs should not exist in DB');
    }
}
