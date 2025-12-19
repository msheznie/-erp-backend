<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderCriteriaAnswerType;

class TenderCriteriaAnswerTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_criteria_answer_types', $tenderCriteriaAnswerType
        );

        $this->assertApiResponse($tenderCriteriaAnswerType);
    }

    /**
     * @test
     */
    public function test_read_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_criteria_answer_types/'.$tenderCriteriaAnswerType->id
        );

        $this->assertApiResponse($tenderCriteriaAnswerType->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->create();
        $editedTenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_criteria_answer_types/'.$tenderCriteriaAnswerType->id,
            $editedTenderCriteriaAnswerType
        );

        $this->assertApiResponse($editedTenderCriteriaAnswerType);
    }

    /**
     * @test
     */
    public function test_delete_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_criteria_answer_types/'.$tenderCriteriaAnswerType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_criteria_answer_types/'.$tenderCriteriaAnswerType->id
        );

        $this->response->assertStatus(404);
    }
}
