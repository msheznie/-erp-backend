<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSTransErrorLog;

class POSTransErrorLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_trans_error_logs', $pOSTransErrorLog
        );

        $this->assertApiResponse($pOSTransErrorLog);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_trans_error_logs/'.$pOSTransErrorLog->id
        );

        $this->assertApiResponse($pOSTransErrorLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->create();
        $editedPOSTransErrorLog = factory(POSTransErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_trans_error_logs/'.$pOSTransErrorLog->id,
            $editedPOSTransErrorLog
        );

        $this->assertApiResponse($editedPOSTransErrorLog);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_trans_error_logs/'.$pOSTransErrorLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_trans_error_logs/'.$pOSTransErrorLog->id
        );

        $this->response->assertStatus(404);
    }
}
