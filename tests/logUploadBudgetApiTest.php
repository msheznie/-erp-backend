<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\logUploadBudget;

class logUploadBudgetApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/log_upload_budgets', $logUploadBudget
        );

        $this->assertApiResponse($logUploadBudget);
    }

    /**
     * @test
     */
    public function test_read_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/log_upload_budgets/'.$logUploadBudget->id
        );

        $this->assertApiResponse($logUploadBudget->toArray());
    }

    /**
     * @test
     */
    public function test_update_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->create();
        $editedlogUploadBudget = factory(logUploadBudget::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/log_upload_budgets/'.$logUploadBudget->id,
            $editedlogUploadBudget
        );

        $this->assertApiResponse($editedlogUploadBudget);
    }

    /**
     * @test
     */
    public function test_delete_log_upload_budget()
    {
        $logUploadBudget = factory(logUploadBudget::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/log_upload_budgets/'.$logUploadBudget->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/log_upload_budgets/'.$logUploadBudget->id
        );

        $this->response->assertStatus(404);
    }
}
