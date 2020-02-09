<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeErpPrintTemplateMasterTrait;
use Tests\ApiTestTrait;

class ErpPrintTemplateMasterApiTest extends TestCase
{
    use MakeErpPrintTemplateMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->fakeErpPrintTemplateMasterData();
        $this->response = $this->json('POST', '/api/erpPrintTemplateMasters', $erpPrintTemplateMaster);

        $this->assertApiResponse($erpPrintTemplateMaster);
    }

    /**
     * @test
     */
    public function test_read_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->makeErpPrintTemplateMaster();
        $this->response = $this->json('GET', '/api/erpPrintTemplateMasters/'.$erpPrintTemplateMaster->id);

        $this->assertApiResponse($erpPrintTemplateMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->makeErpPrintTemplateMaster();
        $editedErpPrintTemplateMaster = $this->fakeErpPrintTemplateMasterData();

        $this->response = $this->json('PUT', '/api/erpPrintTemplateMasters/'.$erpPrintTemplateMaster->id, $editedErpPrintTemplateMaster);

        $this->assertApiResponse($editedErpPrintTemplateMaster);
    }

    /**
     * @test
     */
    public function test_delete_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->makeErpPrintTemplateMaster();
        $this->response = $this->json('DELETE', '/api/erpPrintTemplateMasters/'.$erpPrintTemplateMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/erpPrintTemplateMasters/'.$erpPrintTemplateMaster->id);

        $this->response->assertStatus(404);
    }
}
