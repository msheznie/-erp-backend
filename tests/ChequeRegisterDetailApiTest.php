<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChequeRegisterDetailTrait;
use Tests\ApiTestTrait;

class ChequeRegisterDetailApiTest extends TestCase
{
    use MakeChequeRegisterDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->fakeChequeRegisterDetailData();
        $this->response = $this->json('POST', '/api/chequeRegisterDetails', $chequeRegisterDetail);

        $this->assertApiResponse($chequeRegisterDetail);
    }

    /**
     * @test
     */
    public function test_read_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->makeChequeRegisterDetail();
        $this->response = $this->json('GET', '/api/chequeRegisterDetails/'.$chequeRegisterDetail->id);

        $this->assertApiResponse($chequeRegisterDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->makeChequeRegisterDetail();
        $editedChequeRegisterDetail = $this->fakeChequeRegisterDetailData();

        $this->response = $this->json('PUT', '/api/chequeRegisterDetails/'.$chequeRegisterDetail->id, $editedChequeRegisterDetail);

        $this->assertApiResponse($editedChequeRegisterDetail);
    }

    /**
     * @test
     */
    public function test_delete_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->makeChequeRegisterDetail();
        $this->response = $this->json('DELETE', '/api/chequeRegisterDetails/'.$chequeRegisterDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/chequeRegisterDetails/'.$chequeRegisterDetail->id);

        $this->response->assertStatus(404);
    }
}
