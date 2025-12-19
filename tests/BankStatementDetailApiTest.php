<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankStatementDetail;

class BankStatementDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bank_statement_details', $bankStatementDetail
        );

        $this->assertApiResponse($bankStatementDetail);
    }

    /**
     * @test
     */
    public function test_read_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bank_statement_details/'.$bankStatementDetail->id
        );

        $this->assertApiResponse($bankStatementDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->create();
        $editedBankStatementDetail = factory(BankStatementDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bank_statement_details/'.$bankStatementDetail->id,
            $editedBankStatementDetail
        );

        $this->assertApiResponse($editedBankStatementDetail);
    }

    /**
     * @test
     */
    public function test_delete_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bank_statement_details/'.$bankStatementDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bank_statement_details/'.$bankStatementDetail->id
        );

        $this->response->assertStatus(404);
    }
}
