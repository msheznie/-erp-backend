<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\YesNoMinusSelectionLanguage;

class YesNoMinusSelectionLanguageApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/yes_no_minus_selection_languages', $yesNoMinusSelectionLanguage
        );

        $this->assertApiResponse($yesNoMinusSelectionLanguage);
    }

    /**
     * @test
     */
    public function test_read_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/yes_no_minus_selection_languages/'.$yesNoMinusSelectionLanguage->id
        );

        $this->assertApiResponse($yesNoMinusSelectionLanguage->toArray());
    }

    /**
     * @test
     */
    public function test_update_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->create();
        $editedYesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/yes_no_minus_selection_languages/'.$yesNoMinusSelectionLanguage->id,
            $editedYesNoMinusSelectionLanguage
        );

        $this->assertApiResponse($editedYesNoMinusSelectionLanguage);
    }

    /**
     * @test
     */
    public function test_delete_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/yes_no_minus_selection_languages/'.$yesNoMinusSelectionLanguage->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/yes_no_minus_selection_languages/'.$yesNoMinusSelectionLanguage->id
        );

        $this->response->assertStatus(404);
    }
}
