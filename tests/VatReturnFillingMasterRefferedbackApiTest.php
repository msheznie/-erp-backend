<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFillingMasterRefferedback;

class VatReturnFillingMasterRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filling_master_refferedbacks', $vatReturnFillingMasterRefferedback
        );

        $this->assertApiResponse($vatReturnFillingMasterRefferedback);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_master_refferedbacks/'.$vatReturnFillingMasterRefferedback->id
        );

        $this->assertApiResponse($vatReturnFillingMasterRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->create();
        $editedVatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filling_master_refferedbacks/'.$vatReturnFillingMasterRefferedback->id,
            $editedVatReturnFillingMasterRefferedback
        );

        $this->assertApiResponse($editedVatReturnFillingMasterRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filling_master_refferedbacks/'.$vatReturnFillingMasterRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_master_refferedbacks/'.$vatReturnFillingMasterRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}
