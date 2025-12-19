<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ChequeTemplateMaster;

class ChequeTemplateMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cheque_template_masters', $chequeTemplateMaster
        );

        $this->assertApiResponse($chequeTemplateMaster);
    }

    /**
     * @test
     */
    public function test_read_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cheque_template_masters/'.$chequeTemplateMaster->id
        );

        $this->assertApiResponse($chequeTemplateMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->create();
        $editedChequeTemplateMaster = factory(ChequeTemplateMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cheque_template_masters/'.$chequeTemplateMaster->id,
            $editedChequeTemplateMaster
        );

        $this->assertApiResponse($editedChequeTemplateMaster);
    }

    /**
     * @test
     */
    public function test_delete_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cheque_template_masters/'.$chequeTemplateMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cheque_template_masters/'.$chequeTemplateMaster->id
        );

        $this->response->assertStatus(404);
    }
}
