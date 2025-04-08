<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankStatementMaster;

class BankStatementMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bank_statement_masters', $bankStatementMaster
        );

        $this->assertApiResponse($bankStatementMaster);
    }

    /**
     * @test
     */
    public function test_read_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bank_statement_masters/'.$bankStatementMaster->id
        );

        $this->assertApiResponse($bankStatementMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->create();
        $editedBankStatementMaster = factory(BankStatementMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bank_statement_masters/'.$bankStatementMaster->id,
            $editedBankStatementMaster
        );

        $this->assertApiResponse($editedBankStatementMaster);
    }

    /**
     * @test
     */
    public function test_delete_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bank_statement_masters/'.$bankStatementMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bank_statement_masters/'.$bankStatementMaster->id
        );

        $this->response->assertStatus(404);
    }
}
