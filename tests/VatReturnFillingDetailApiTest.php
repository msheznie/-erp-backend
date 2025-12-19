<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFillingDetail;

class VatReturnFillingDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filling_details', $vatReturnFillingDetail
        );

        $this->assertApiResponse($vatReturnFillingDetail);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_details/'.$vatReturnFillingDetail->id
        );

        $this->assertApiResponse($vatReturnFillingDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->create();
        $editedVatReturnFillingDetail = factory(VatReturnFillingDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filling_details/'.$vatReturnFillingDetail->id,
            $editedVatReturnFillingDetail
        );

        $this->assertApiResponse($editedVatReturnFillingDetail);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filling_details/'.$vatReturnFillingDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_details/'.$vatReturnFillingDetail->id
        );

        $this->response->assertStatus(404);
    }
}
