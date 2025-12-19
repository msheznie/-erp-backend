<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PdcLog;

class PdcLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pdc_logs', $pdcLog
        );

        $this->assertApiResponse($pdcLog);
    }

    /**
     * @test
     */
    public function test_read_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pdc_logs/'.$pdcLog->id
        );

        $this->assertApiResponse($pdcLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->create();
        $editedPdcLog = factory(PdcLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pdc_logs/'.$pdcLog->id,
            $editedPdcLog
        );

        $this->assertApiResponse($editedPdcLog);
    }

    /**
     * @test
     */
    public function test_delete_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pdc_logs/'.$pdcLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pdc_logs/'.$pdcLog->id
        );

        $this->response->assertStatus(404);
    }
}
