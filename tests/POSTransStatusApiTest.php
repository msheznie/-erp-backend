<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSTransStatus;

class POSTransStatusApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_trans_statuses', $pOSTransStatus
        );

        $this->assertApiResponse($pOSTransStatus);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_trans_statuses/'.$pOSTransStatus->id
        );

        $this->assertApiResponse($pOSTransStatus->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->create();
        $editedPOSTransStatus = factory(POSTransStatus::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_trans_statuses/'.$pOSTransStatus->id,
            $editedPOSTransStatus
        );

        $this->assertApiResponse($editedPOSTransStatus);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_trans_statuses/'.$pOSTransStatus->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_trans_statuses/'.$pOSTransStatus->id
        );

        $this->response->assertStatus(404);
    }
}
