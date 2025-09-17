<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeTemplate;

class FinalReturnIncomeTemplateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_templates', $finalReturnIncomeTemplate
        );

        $this->assertApiResponse($finalReturnIncomeTemplate);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_templates/'.$finalReturnIncomeTemplate->id
        );

        $this->assertApiResponse($finalReturnIncomeTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->create();
        $editedFinalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_templates/'.$finalReturnIncomeTemplate->id,
            $editedFinalReturnIncomeTemplate
        );

        $this->assertApiResponse($editedFinalReturnIncomeTemplate);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_templates/'.$finalReturnIncomeTemplate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_templates/'.$finalReturnIncomeTemplate->id
        );

        $this->response->assertStatus(404);
    }
}
