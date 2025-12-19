<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ChequeUpdateReason;

class ChequeUpdateReasonApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cheque_update_reasons', $chequeUpdateReason
        );

        $this->assertApiResponse($chequeUpdateReason);
    }

    /**
     * @test
     */
    public function test_read_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cheque_update_reasons/'.$chequeUpdateReason->id
        );

        $this->assertApiResponse($chequeUpdateReason->toArray());
    }

    /**
     * @test
     */
    public function test_update_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->create();
        $editedChequeUpdateReason = factory(ChequeUpdateReason::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cheque_update_reasons/'.$chequeUpdateReason->id,
            $editedChequeUpdateReason
        );

        $this->assertApiResponse($editedChequeUpdateReason);
    }

    /**
     * @test
     */
    public function test_delete_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cheque_update_reasons/'.$chequeUpdateReason->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cheque_update_reasons/'.$chequeUpdateReason->id
        );

        $this->response->assertStatus(404);
    }
}
