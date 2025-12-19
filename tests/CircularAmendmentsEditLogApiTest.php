<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CircularAmendmentsEditLog;

class CircularAmendmentsEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/circular_amendments_edit_logs', $circularAmendmentsEditLog
        );

        $this->assertApiResponse($circularAmendmentsEditLog);
    }

    /**
     * @test
     */
    public function test_read_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/circular_amendments_edit_logs/'.$circularAmendmentsEditLog->id
        );

        $this->assertApiResponse($circularAmendmentsEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->create();
        $editedCircularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/circular_amendments_edit_logs/'.$circularAmendmentsEditLog->id,
            $editedCircularAmendmentsEditLog
        );

        $this->assertApiResponse($editedCircularAmendmentsEditLog);
    }

    /**
     * @test
     */
    public function test_delete_circular_amendments_edit_log()
    {
        $circularAmendmentsEditLog = factory(CircularAmendmentsEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/circular_amendments_edit_logs/'.$circularAmendmentsEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/circular_amendments_edit_logs/'.$circularAmendmentsEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
