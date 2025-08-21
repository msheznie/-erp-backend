<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeTemplateDetails;

class FinalReturnIncomeTemplateDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_template_details', $finalReturnIncomeTemplateDetails
        );

        $this->assertApiResponse($finalReturnIncomeTemplateDetails);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_template_details/'.$finalReturnIncomeTemplateDetails->id
        );

        $this->assertApiResponse($finalReturnIncomeTemplateDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->create();
        $editedFinalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_template_details/'.$finalReturnIncomeTemplateDetails->id,
            $editedFinalReturnIncomeTemplateDetails
        );

        $this->assertApiResponse($editedFinalReturnIncomeTemplateDetails);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_template_details/'.$finalReturnIncomeTemplateDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_template_details/'.$finalReturnIncomeTemplateDetails->id
        );

        $this->response->assertStatus(404);
    }
}
