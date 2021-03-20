<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMESystemEmployeeType;

class SMESystemEmployeeTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_system_employee_types', $sMESystemEmployeeType
        );

        $this->assertApiResponse($sMESystemEmployeeType);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_system_employee_types/'.$sMESystemEmployeeType->id
        );

        $this->assertApiResponse($sMESystemEmployeeType->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->create();
        $editedSMESystemEmployeeType = factory(SMESystemEmployeeType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_system_employee_types/'.$sMESystemEmployeeType->id,
            $editedSMESystemEmployeeType
        );

        $this->assertApiResponse($editedSMESystemEmployeeType);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_system_employee_types/'.$sMESystemEmployeeType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_system_employee_types/'.$sMESystemEmployeeType->id
        );

        $this->response->assertStatus(404);
    }
}
