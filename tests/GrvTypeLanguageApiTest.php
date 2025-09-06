<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\GrvTypeLanguage;

class GrvTypeLanguageApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/grv_type_languages', $grvTypeLanguage
        );

        $this->assertApiResponse($grvTypeLanguage);
    }

    /**
     * @test
     */
    public function test_read_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/grv_type_languages/'.$grvTypeLanguage->id
        );

        $this->assertApiResponse($grvTypeLanguage->toArray());
    }

    /**
     * @test
     */
    public function test_update_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->create();
        $editedGrvTypeLanguage = factory(GrvTypeLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/grv_type_languages/'.$grvTypeLanguage->id,
            $editedGrvTypeLanguage
        );

        $this->assertApiResponse($editedGrvTypeLanguage);
    }

    /**
     * @test
     */
    public function test_delete_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/grv_type_languages/'.$grvTypeLanguage->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/grv_type_languages/'.$grvTypeLanguage->id
        );

        $this->response->assertStatus(404);
    }
}
