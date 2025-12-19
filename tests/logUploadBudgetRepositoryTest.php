<?php namespace Tests\Repositories;

use App\Models\logUploadBudget;
use App\Repositories\logUploadBudgetRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class logUploadBudgetRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var logUploadBudgetRepository
     */
    protected $logUploadBudgetRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->logUploadBudgetRepo = \App::make(logUploadBudgetRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->make()->toArray();

        $createdlogUploadBudget = $this->logUploadBudgetRepo->create($logUploadBudget);

        $createdlogUploadBudget = $createdlogUploadBudget->toArray();
        $this->assertArrayHasKey('id', $createdlogUploadBudget);
        $this->assertNotNull($createdlogUploadBudget['id'], 'Created logUploadBudget must have id specified');
        $this->assertNotNull(logUploadBudget::find($createdlogUploadBudget['id']), 'logUploadBudget with given id must be in DB');
        $this->assertModelData($logUploadBudget, $createdlogUploadBudget);
    }

    /**
     * @test read
     */
    public function test_read_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->create();

        $dblogUploadBudget = $this->logUploadBudgetRepo->find($logUploadBudget->id);

        $dblogUploadBudget = $dblogUploadBudget->toArray();
        $this->assertModelData($logUploadBudget->toArray(), $dblogUploadBudget);
    }

    /**
     * @test update
     */
    public function test_update_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->create();
        $fakelogUploadBudget = factory(logUploadBudget::class)->make()->toArray();

        $updatedlogUploadBudget = $this->logUploadBudgetRepo->update($fakelogUploadBudget, $logUploadBudget->id);

        $this->assertModelData($fakelogUploadBudget, $updatedlogUploadBudget->toArray());
        $dblogUploadBudget = $this->logUploadBudgetRepo->find($logUploadBudget->id);
        $this->assertModelData($fakelogUploadBudget, $dblogUploadBudget->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->create();

        $resp = $this->logUploadBudgetRepo->delete($logUploadBudget->id);

        $this->assertTrue($resp);
        $this->assertNull(logUploadBudget::find($logUploadBudget->id), 'logUploadBudget should not exist in DB');
    }
}
