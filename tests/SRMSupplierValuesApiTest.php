<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMSupplierValues;

class SRMSupplierValuesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_supplier_values', $sRMSupplierValues
        );

        $this->assertApiResponse($sRMSupplierValues);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_supplier_values/'.$sRMSupplierValues->id
        );

        $this->assertApiResponse($sRMSupplierValues->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->create();
        $editedSRMSupplierValues = factory(SRMSupplierValues::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_supplier_values/'.$sRMSupplierValues->id,
            $editedSRMSupplierValues
        );

        $this->assertApiResponse($editedSRMSupplierValues);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_supplier_values/'.$sRMSupplierValues->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_supplier_values/'.$sRMSupplierValues->id
        );

        $this->response->assertStatus(404);
    }
}
