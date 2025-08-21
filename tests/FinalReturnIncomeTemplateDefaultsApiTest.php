<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeTemplateDefaults;

class FinalReturnIncomeTemplateDefaultsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_template_defaults', $finalReturnIncomeTemplateDefaults
        );

        $this->assertApiResponse($finalReturnIncomeTemplateDefaults);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_template_defaults/'.$finalReturnIncomeTemplateDefaults->id
        );

        $this->assertApiResponse($finalReturnIncomeTemplateDefaults->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->create();
        $editedFinalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_template_defaults/'.$finalReturnIncomeTemplateDefaults->id,
            $editedFinalReturnIncomeTemplateDefaults
        );

        $this->assertApiResponse($editedFinalReturnIncomeTemplateDefaults);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_template_defaults/'.$finalReturnIncomeTemplateDefaults->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_template_defaults/'.$finalReturnIncomeTemplateDefaults->id
        );

        $this->response->assertStatus(404);
    }
}
