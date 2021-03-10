<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEEmpContractType;

class SMEEmpContractTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_emp_contract_types', $sMEEmpContractType
        );

        $this->assertApiResponse($sMEEmpContractType);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_emp_contract_types/'.$sMEEmpContractType->id
        );

        $this->assertApiResponse($sMEEmpContractType->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->create();
        $editedSMEEmpContractType = factory(SMEEmpContractType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_emp_contract_types/'.$sMEEmpContractType->id,
            $editedSMEEmpContractType
        );

        $this->assertApiResponse($editedSMEEmpContractType);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_emp_contract_types/'.$sMEEmpContractType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_emp_contract_types/'.$sMEEmpContractType->id
        );

        $this->response->assertStatus(404);
    }
}
