<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpErpPayShiftMaster;

class SrpErpPayShiftMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_erp_pay_shift_masters', $srpErpPayShiftMaster
        );

        $this->assertApiResponse($srpErpPayShiftMaster);
    }

    /**
     * @test
     */
    public function test_read_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_erp_pay_shift_masters/'.$srpErpPayShiftMaster->id
        );

        $this->assertApiResponse($srpErpPayShiftMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->create();
        $editedSrpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_erp_pay_shift_masters/'.$srpErpPayShiftMaster->id,
            $editedSrpErpPayShiftMaster
        );

        $this->assertApiResponse($editedSrpErpPayShiftMaster);
    }

    /**
     * @test
     */
    public function test_delete_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_erp_pay_shift_masters/'.$srpErpPayShiftMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_erp_pay_shift_masters/'.$srpErpPayShiftMaster->id
        );

        $this->response->assertStatus(404);
    }
}
