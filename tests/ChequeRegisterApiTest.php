<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChequeRegisterTrait;
use Tests\ApiTestTrait;

class ChequeRegisterApiTest extends TestCase
{
    use MakeChequeRegisterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cheque_register()
    {
        $chequeRegister = $this->fakeChequeRegisterData();
        $this->response = $this->json('POST', '/api/chequeRegisters', $chequeRegister);

        $this->assertApiResponse($chequeRegister);
    }

    /**
     * @test
     */
    public function test_read_cheque_register()
    {
        $chequeRegister = $this->makeChequeRegister();
        $this->response = $this->json('GET', '/api/chequeRegisters/'.$chequeRegister->id);

        $this->assertApiResponse($chequeRegister->toArray());
    }

    /**
     * @test
     */
    public function test_update_cheque_register()
    {
        $chequeRegister = $this->makeChequeRegister();
        $editedChequeRegister = $this->fakeChequeRegisterData();

        $this->response = $this->json('PUT', '/api/chequeRegisters/'.$chequeRegister->id, $editedChequeRegister);

        $this->assertApiResponse($editedChequeRegister);
    }

    /**
     * @test
     */
    public function test_delete_cheque_register()
    {
        $chequeRegister = $this->makeChequeRegister();
        $this->response = $this->json('DELETE', '/api/chequeRegisters/'.$chequeRegister->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/chequeRegisters/'.$chequeRegister->id);

        $this->response->assertStatus(404);
    }
}
