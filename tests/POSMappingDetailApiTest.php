<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSMappingDetail;

class POSMappingDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_mapping_details', $pOSMappingDetail
        );

        $this->assertApiResponse($pOSMappingDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_mapping_details/'.$pOSMappingDetail->id
        );

        $this->assertApiResponse($pOSMappingDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->create();
        $editedPOSMappingDetail = factory(POSMappingDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_mapping_details/'.$pOSMappingDetail->id,
            $editedPOSMappingDetail
        );

        $this->assertApiResponse($editedPOSMappingDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_mapping_details/'.$pOSMappingDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_mapping_details/'.$pOSMappingDetail->id
        );

        $this->response->assertStatus(404);
    }
}
