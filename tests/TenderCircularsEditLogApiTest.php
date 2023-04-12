<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderCircularsEditLog;

class TenderCircularsEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_circulars_edit_logs', $tenderCircularsEditLog
        );

        $this->assertApiResponse($tenderCircularsEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_circulars_edit_logs/'.$tenderCircularsEditLog->id
        );

        $this->assertApiResponse($tenderCircularsEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->create();
        $editedTenderCircularsEditLog = factory(TenderCircularsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_circulars_edit_logs/'.$tenderCircularsEditLog->id,
            $editedTenderCircularsEditLog
        );

        $this->assertApiResponse($editedTenderCircularsEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_circulars_edit_logs/'.$tenderCircularsEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_circulars_edit_logs/'.$tenderCircularsEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
