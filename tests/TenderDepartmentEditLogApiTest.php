<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderDepartmentEditLog;

class TenderDepartmentEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_department_edit_logs', $tenderDepartmentEditLog
        );

        $this->assertApiResponse($tenderDepartmentEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_department_edit_logs/'.$tenderDepartmentEditLog->id
        );

        $this->assertApiResponse($tenderDepartmentEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->create();
        $editedTenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_department_edit_logs/'.$tenderDepartmentEditLog->id,
            $editedTenderDepartmentEditLog
        );

        $this->assertApiResponse($editedTenderDepartmentEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_department_edit_logs/'.$tenderDepartmentEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_department_edit_logs/'.$tenderDepartmentEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
