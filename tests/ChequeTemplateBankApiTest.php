<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ChequeTemplateBank;

class ChequeTemplateBankApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cheque_template_banks', $chequeTemplateBank
        );

        $this->assertApiResponse($chequeTemplateBank);
    }

    /**
     * @test
     */
    public function test_read_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cheque_template_banks/'.$chequeTemplateBank->id
        );

        $this->assertApiResponse($chequeTemplateBank->toArray());
    }

    /**
     * @test
     */
    public function test_update_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->create();
        $editedChequeTemplateBank = factory(ChequeTemplateBank::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cheque_template_banks/'.$chequeTemplateBank->id,
            $editedChequeTemplateBank
        );

        $this->assertApiResponse($editedChequeTemplateBank);
    }

    /**
     * @test
     */
    public function test_delete_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cheque_template_banks/'.$chequeTemplateBank->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cheque_template_banks/'.$chequeTemplateBank->id
        );

        $this->response->assertStatus(404);
    }
}
