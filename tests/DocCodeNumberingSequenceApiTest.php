<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocCodeNumberingSequence;

class DocCodeNumberingSequenceApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/doc_code_numbering_sequences', $docCodeNumberingSequence
        );

        $this->assertApiResponse($docCodeNumberingSequence);
    }

    /**
     * @test
     */
    public function test_read_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/doc_code_numbering_sequences/'.$docCodeNumberingSequence->id
        );

        $this->assertApiResponse($docCodeNumberingSequence->toArray());
    }

    /**
     * @test
     */
    public function test_update_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->create();
        $editedDocCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/doc_code_numbering_sequences/'.$docCodeNumberingSequence->id,
            $editedDocCodeNumberingSequence
        );

        $this->assertApiResponse($editedDocCodeNumberingSequence);
    }

    /**
     * @test
     */
    public function test_delete_doc_code_numbering_sequence()
    {
        $docCodeNumberingSequence = factory(DocCodeNumberingSequence::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/doc_code_numbering_sequences/'.$docCodeNumberingSequence->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/doc_code_numbering_sequences/'.$docCodeNumberingSequence->id
        );

        $this->response->assertStatus(404);
    }
}
