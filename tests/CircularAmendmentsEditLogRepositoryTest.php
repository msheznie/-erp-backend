<?php namespace Tests\Repositories;

use App\Models\CircularAmendmentsEditLog;
use App\Repositories\CircularAmendmentsEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CircularAmendmentsEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CircularAmendmentsEditLogRepository
     */
    protected $circularAmendmentsEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->circularAmendmentsEditLogRepo = \App::make(CircularAmendmentsEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->make()->toArray();

        $createdCircularAmendmentsEditLog = $this->circularAmendmentsEditLogRepo->create($circularAmendmentsEditLog);

        $createdCircularAmendmentsEditLog = $createdCircularAmendmentsEditLog->toArray();
        $this->assertArrayHasKey('id', $createdCircularAmendmentsEditLog);
        $this->assertNotNull($createdCircularAmendmentsEditLog['id'], 'Created CircularAmendmentsEditLog must have id specified');
        $this->assertNotNull(CircularAmendmentsEditLog::find($createdCircularAmendmentsEditLog['id']), 'CircularAmendmentsEditLog with given id must be in DB');
        $this->assertModelData($circularAmendmentsEditLog, $createdCircularAmendmentsEditLog);
    }

    /**
     * @test read
     */
    public function test_read_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->create();

        $dbCircularAmendmentsEditLog = $this->circularAmendmentsEditLogRepo->find($circularAmendmentsEditLog->id);

        $dbCircularAmendmentsEditLog = $dbCircularAmendmentsEditLog->toArray();
        $this->assertModelData($circularAmendmentsEditLog->toArray(), $dbCircularAmendmentsEditLog);
    }

    /**
     * @test update
     */
    public function test_update_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->create();
        $fakeCircularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->make()->toArray();

        $updatedCircularAmendmentsEditLog = $this->circularAmendmentsEditLogRepo->update($fakeCircularAmendmentsEditLog, $circularAmendmentsEditLog->id);

        $this->assertModelData($fakeCircularAmendmentsEditLog, $updatedCircularAmendmentsEditLog->toArray());
        $dbCircularAmendmentsEditLog = $this->circularAmendmentsEditLogRepo->find($circularAmendmentsEditLog->id);
        $this->assertModelData($fakeCircularAmendmentsEditLog, $dbCircularAmendmentsEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->create();

        $resp = $this->circularAmendmentsEditLogRepo->delete($circularAmendmentsEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(CircularAmendmentsEditLog::find($circularAmendmentsEditLog->id), 'CircularAmendmentsEditLog should not exist in DB');
    }
}
