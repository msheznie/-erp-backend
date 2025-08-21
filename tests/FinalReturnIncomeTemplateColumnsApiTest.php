<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeTemplateColumns;

class FinalReturnIncomeTemplateColumnsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_template_columns', $finalReturnIncomeTemplateColumns
        );

        $this->assertApiResponse($finalReturnIncomeTemplateColumns);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_template_columns/'.$finalReturnIncomeTemplateColumns->id
        );

        $this->assertApiResponse($finalReturnIncomeTemplateColumns->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->create();
        $editedFinalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_template_columns/'.$finalReturnIncomeTemplateColumns->id,
            $editedFinalReturnIncomeTemplateColumns
        );

        $this->assertApiResponse($editedFinalReturnIncomeTemplateColumns);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_template_columns/'.$finalReturnIncomeTemplateColumns->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_template_columns/'.$finalReturnIncomeTemplateColumns->id
        );

        $this->response->assertStatus(404);
    }
}
