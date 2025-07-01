<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CircularSuppliersEditLog;

class CircularSuppliersEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/circular_suppliers_edit_logs', $circularSuppliersEditLog
        );

        $this->assertApiResponse($circularSuppliersEditLog);
    }

    /**
     * @test
     */
    public function test_read_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/circular_suppliers_edit_logs/'.$circularSuppliersEditLog->id
        );

        $this->assertApiResponse($circularSuppliersEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->create();
        $editedCircularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/circular_suppliers_edit_logs/'.$circularSuppliersEditLog->id,
            $editedCircularSuppliersEditLog
        );

        $this->assertApiResponse($editedCircularSuppliersEditLog);
    }

    /**
     * @test
     */
    public function test_delete_circular_suppliers_edit_log()
    {
        $circularSuppliersEditLog = factory(CircularSuppliersEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/circular_suppliers_edit_logs/'.$circularSuppliersEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/circular_suppliers_edit_logs/'.$circularSuppliersEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
