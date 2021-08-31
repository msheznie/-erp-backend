<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HREmpContractHistory;

class HREmpContractHistoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/h_r_emp_contract_histories', $hREmpContractHistory
        );

        $this->assertApiResponse($hREmpContractHistory);
    }

    /**
     * @test
     */
    public function test_read_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/h_r_emp_contract_histories/'.$hREmpContractHistory->id
        );

        $this->assertApiResponse($hREmpContractHistory->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->create();
        $editedHREmpContractHistory = factory(HREmpContractHistory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/h_r_emp_contract_histories/'.$hREmpContractHistory->id,
            $editedHREmpContractHistory
        );

        $this->assertApiResponse($editedHREmpContractHistory);
    }

    /**
     * @test
     */
    public function test_delete_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/h_r_emp_contract_histories/'.$hREmpContractHistory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/h_r_emp_contract_histories/'.$hREmpContractHistory->id
        );

        $this->response->assertStatus(404);
    }
}
