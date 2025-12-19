<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProcumentActivity;

class ProcumentActivityApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/procument_activities', $procumentActivity
        );

        $this->assertApiResponse($procumentActivity);
    }

    /**
     * @test
     */
    public function test_read_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/procument_activities/'.$procumentActivity->id
        );

        $this->assertApiResponse($procumentActivity->toArray());
    }

    /**
     * @test
     */
    public function test_update_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->create();
        $editedProcumentActivity = factory(ProcumentActivity::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/procument_activities/'.$procumentActivity->id,
            $editedProcumentActivity
        );

        $this->assertApiResponse($editedProcumentActivity);
    }

    /**
     * @test
     */
    public function test_delete_procument_activity()
    {
        $procumentActivity = factory(ProcumentActivity::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/procument_activities/'.$procumentActivity->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/procument_activities/'.$procumentActivity->id
        );

        $this->response->assertStatus(404);
    }
}
