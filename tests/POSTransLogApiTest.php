<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSTransLog;

class POSTransLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_trans_logs', $pOSTransLog
        );

        $this->assertApiResponse($pOSTransLog);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_trans_logs/'.$pOSTransLog->id
        );

        $this->assertApiResponse($pOSTransLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->create();
        $editedPOSTransLog = factory(POSTransLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_trans_logs/'.$pOSTransLog->id,
            $editedPOSTransLog
        );

        $this->assertApiResponse($editedPOSTransLog);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_trans_logs/'.$pOSTransLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_trans_logs/'.$pOSTransLog->id
        );

        $this->response->assertStatus(404);
    }
}
