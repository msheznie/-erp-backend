<?php namespace Tests\Repositories;

use App\Models\CircularSuppliersEditLog;
use App\Repositories\CircularSuppliersEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CircularSuppliersEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CircularSuppliersEditLogRepository
     */
    protected $circularSuppliersEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->circularSuppliersEditLogRepo = \App::make(CircularSuppliersEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->make()->toArray();

        $createdCircularSuppliersEditLog = $this->circularSuppliersEditLogRepo->create($circularSuppliersEditLog);

        $createdCircularSuppliersEditLog = $createdCircularSuppliersEditLog->toArray();
        $this->assertArrayHasKey('id', $createdCircularSuppliersEditLog);
        $this->assertNotNull($createdCircularSuppliersEditLog['id'], 'Created CircularSuppliersEditLog must have id specified');
        $this->assertNotNull(CircularSuppliersEditLog::find($createdCircularSuppliersEditLog['id']), 'CircularSuppliersEditLog with given id must be in DB');
        $this->assertModelData($circularSuppliersEditLog, $createdCircularSuppliersEditLog);
    }

    /**
     * @test read
     */
    public function test_read_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->create();

        $dbCircularSuppliersEditLog = $this->circularSuppliersEditLogRepo->find($circularSuppliersEditLog->id);

        $dbCircularSuppliersEditLog = $dbCircularSuppliersEditLog->toArray();
        $this->assertModelData($circularSuppliersEditLog->toArray(), $dbCircularSuppliersEditLog);
    }

    /**
     * @test update
     */
    public function test_update_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->create();
        $fakeCircularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->make()->toArray();

        $updatedCircularSuppliersEditLog = $this->circularSuppliersEditLogRepo->update($fakeCircularSuppliersEditLog, $circularSuppliersEditLog->id);

        $this->assertModelData($fakeCircularSuppliersEditLog, $updatedCircularSuppliersEditLog->toArray());
        $dbCircularSuppliersEditLog = $this->circularSuppliersEditLogRepo->find($circularSuppliersEditLog->id);
        $this->assertModelData($fakeCircularSuppliersEditLog, $dbCircularSuppliersEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->create();

        $resp = $this->circularSuppliersEditLogRepo->delete($circularSuppliersEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(CircularSuppliersEditLog::find($circularSuppliersEditLog->id), 'CircularSuppliersEditLog should not exist in DB');
    }
}
