<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalIncomeTemplateDefaultTranslation;

class FinalIncomeTemplateDefaultTranslationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_income_template_default_translations', $finalIncomeTemplateDefaultTranslation
        );

        $this->assertApiResponse($finalIncomeTemplateDefaultTranslation);
    }

    /**
     * @test
     */
    public function test_read_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_income_template_default_translations/'.$finalIncomeTemplateDefaultTranslation->id
        );

        $this->assertApiResponse($finalIncomeTemplateDefaultTranslation->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->create();
        $editedFinalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_income_template_default_translations/'.$finalIncomeTemplateDefaultTranslation->id,
            $editedFinalIncomeTemplateDefaultTranslation
        );

        $this->assertApiResponse($editedFinalIncomeTemplateDefaultTranslation);
    }

    /**
     * @test
     */
    public function test_delete_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_income_template_default_translations/'.$finalIncomeTemplateDefaultTranslation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_income_template_default_translations/'.$finalIncomeTemplateDefaultTranslation->id
        );

        $this->response->assertStatus(404);
    }
}
