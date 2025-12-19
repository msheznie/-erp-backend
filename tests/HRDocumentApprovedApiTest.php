<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HRDocumentApproved;

class HRDocumentApprovedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/h_r_document_approveds', $hRDocumentApproved
        );

        $this->assertApiResponse($hRDocumentApproved);
    }

    /**
     * @test
     */
    public function test_read_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/h_r_document_approveds/'.$hRDocumentApproved->id
        );

        $this->assertApiResponse($hRDocumentApproved->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->create();
        $editedHRDocumentApproved = factory(HRDocumentApproved::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/h_r_document_approveds/'.$hRDocumentApproved->id,
            $editedHRDocumentApproved
        );

        $this->assertApiResponse($editedHRDocumentApproved);
    }

    /**
     * @test
     */
    public function test_delete_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/h_r_document_approveds/'.$hRDocumentApproved->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/h_r_document_approveds/'.$hRDocumentApproved->id
        );

        $this->response->assertStatus(404);
    }
}
