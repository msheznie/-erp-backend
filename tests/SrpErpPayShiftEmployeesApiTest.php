<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpErpPayShiftEmployees;

class SrpErpPayShiftEmployeesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_erp_pay_shift_employees', $srpErpPayShiftEmployees
        );

        $this->assertApiResponse($srpErpPayShiftEmployees);
    }

    /**
     * @test
     */
    public function test_read_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_erp_pay_shift_employees/'.$srpErpPayShiftEmployees->id
        );

        $this->assertApiResponse($srpErpPayShiftEmployees->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->create();
        $editedSrpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_erp_pay_shift_employees/'.$srpErpPayShiftEmployees->id,
            $editedSrpErpPayShiftEmployees
        );

        $this->assertApiResponse($editedSrpErpPayShiftEmployees);
    }

    /**
     * @test
     */
    public function test_delete_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_erp_pay_shift_employees/'.$srpErpPayShiftEmployees->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_erp_pay_shift_employees/'.$srpErpPayShiftEmployees->id
        );

        $this->response->assertStatus(404);
    }
}
