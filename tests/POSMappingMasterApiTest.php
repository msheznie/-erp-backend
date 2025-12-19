<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSMappingMaster;

class POSMappingMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_mapping_masters', $pOSMappingMaster
        );

        $this->assertApiResponse($pOSMappingMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_mapping_masters/'.$pOSMappingMaster->id
        );

        $this->assertApiResponse($pOSMappingMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->create();
        $editedPOSMappingMaster = factory(POSMappingMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_mapping_masters/'.$pOSMappingMaster->id,
            $editedPOSMappingMaster
        );

        $this->assertApiResponse($editedPOSMappingMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_mapping_masters/'.$pOSMappingMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_mapping_masters/'.$pOSMappingMaster->id
        );

        $this->response->assertStatus(404);
    }
}
