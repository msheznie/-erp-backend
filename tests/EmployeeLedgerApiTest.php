<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EmployeeLedger;

class EmployeeLedgerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/employee_ledgers', $employeeLedger
        );

        $this->assertApiResponse($employeeLedger);
    }

    /**
     * @test
     */
    public function test_read_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/employee_ledgers/'.$employeeLedger->id
        );

        $this->assertApiResponse($employeeLedger->toArray());
    }

    /**
     * @test
     */
    public function test_update_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->create();
        $editedEmployeeLedger = factory(EmployeeLedger::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/employee_ledgers/'.$employeeLedger->id,
            $editedEmployeeLedger
        );

        $this->assertApiResponse($editedEmployeeLedger);
    }

    /**
     * @test
     */
    public function test_delete_employee_ledger()
    {
        $employeeLedger = factory(EmployeeLedger::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/employee_ledgers/'.$employeeLedger->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/employee_ledgers/'.$employeeLedger->id
        );

        $this->response->assertStatus(404);
    }
}
