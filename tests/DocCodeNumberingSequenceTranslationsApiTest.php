<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocCodeNumberingSequenceTranslations;

class DocCodeNumberingSequenceTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/doc_code_numbering_sequence_translations', $docCodeNumberingSequenceTranslations
        );

        $this->assertApiResponse($docCodeNumberingSequenceTranslations);
    }

    /**
     * @test
     */
    public function test_read_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/doc_code_numbering_sequence_translations/'.$docCodeNumberingSequenceTranslations->id
        );

        $this->assertApiResponse($docCodeNumberingSequenceTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->create();
        $editedDocCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/doc_code_numbering_sequence_translations/'.$docCodeNumberingSequenceTranslations->id,
            $editedDocCodeNumberingSequenceTranslations
        );

        $this->assertApiResponse($editedDocCodeNumberingSequenceTranslations);
    }

    /**
     * @test
     */
    public function test_delete_doc_code_numbering_sequence_translations()
    {
        $docCodeNumberingSequenceTranslations = factory(DocCodeNumberingSequenceTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/doc_code_numbering_sequence_translations/'.$docCodeNumberingSequenceTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/doc_code_numbering_sequence_translations/'.$docCodeNumberingSequenceTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
