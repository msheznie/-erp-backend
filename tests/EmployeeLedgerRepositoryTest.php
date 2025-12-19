<?php namespace Tests\Repositories;

use App\Models\EmployeeLedger;
use App\Repositories\EmployeeLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EmployeeLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeLedgerRepository
     */
    protected $employeeLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->employeeLedgerRepo = \App::make(EmployeeLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->make()->toArray();

        $createdEmployeeLedger = $this->employeeLedgerRepo->create($employeeLedger);

        $createdEmployeeLedger = $createdEmployeeLedger->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeLedger);
        $this->assertNotNull($createdEmployeeLedger['id'], 'Created EmployeeLedger must have id specified');
        $this->assertNotNull(EmployeeLedger::find($createdEmployeeLedger['id']), 'EmployeeLedger with given id must be in DB');
        $this->assertModelData($employeeLedger, $createdEmployeeLedger);
    }

    /**
     * @test read
     */
    public function test_read_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->create();

        $dbEmployeeLedger = $this->employeeLedgerRepo->find($employeeLedger->id);

        $dbEmployeeLedger = $dbEmployeeLedger->toArray();
        $this->assertModelData($employeeLedger->toArray(), $dbEmployeeLedger);
    }

    /**
     * @test update
     */
    public function test_update_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->create();
        $fakeEmployeeLedger = factory(EmployeeLedger::class)->make()->toArray();

        $updatedEmployeeLedger = $this->employeeLedgerRepo->update($fakeEmployeeLedger, $employeeLedger->id);

        $this->assertModelData($fakeEmployeeLedger, $updatedEmployeeLedger->toArray());
        $dbEmployeeLedger = $this->employeeLedgerRepo->find($employeeLedger->id);
        $this->assertModelData($fakeEmployeeLedger, $dbEmployeeLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->create();

        $resp = $this->employeeLedgerRepo->delete($employeeLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(EmployeeLedger::find($employeeLedger->id), 'EmployeeLedger should not exist in DB');
    }
}
