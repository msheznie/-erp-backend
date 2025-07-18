<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvacuationCriteriaScoreConfigLog;

class EvacuationCriteriaScoreConfigLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evacuation_criteria_score_config_logs', $evacuationCriteriaScoreConfigLog
        );

        $this->assertApiResponse($evacuationCriteriaScoreConfigLog);
    }

    /**
     * @test
     */
    public function test_read_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evacuation_criteria_score_config_logs/'.$evacuationCriteriaScoreConfigLog->id
        );

        $this->assertApiResponse($evacuationCriteriaScoreConfigLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->create();
        $editedEvacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evacuation_criteria_score_config_logs/'.$evacuationCriteriaScoreConfigLog->id,
            $editedEvacuationCriteriaScoreConfigLog
        );

        $this->assertApiResponse($editedEvacuationCriteriaScoreConfigLog);
    }

    /**
     * @test
     */
    public function test_delete_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evacuation_criteria_score_config_logs/'.$evacuationCriteriaScoreConfigLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evacuation_criteria_score_config_logs/'.$evacuationCriteriaScoreConfigLog->id
        );

        $this->response->assertStatus(404);
    }
}
