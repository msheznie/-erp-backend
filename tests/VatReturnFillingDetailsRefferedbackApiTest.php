<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFillingDetailsRefferedback;

class VatReturnFillingDetailsRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filling_details_refferedbacks', $vatReturnFillingDetailsRefferedback
        );

        $this->assertApiResponse($vatReturnFillingDetailsRefferedback);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_details_refferedbacks/'.$vatReturnFillingDetailsRefferedback->id
        );

        $this->assertApiResponse($vatReturnFillingDetailsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->create();
        $editedVatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filling_details_refferedbacks/'.$vatReturnFillingDetailsRefferedback->id,
            $editedVatReturnFillingDetailsRefferedback
        );

        $this->assertApiResponse($editedVatReturnFillingDetailsRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filling_details_refferedbacks/'.$vatReturnFillingDetailsRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_details_refferedbacks/'.$vatReturnFillingDetailsRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}
