<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFilledCategoryRefferedback;

class VatReturnFilledCategoryRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filled_category_refferedbacks', $vatReturnFilledCategoryRefferedback
        );

        $this->assertApiResponse($vatReturnFilledCategoryRefferedback);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filled_category_refferedbacks/'.$vatReturnFilledCategoryRefferedback->id
        );

        $this->assertApiResponse($vatReturnFilledCategoryRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->create();
        $editedVatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filled_category_refferedbacks/'.$vatReturnFilledCategoryRefferedback->id,
            $editedVatReturnFilledCategoryRefferedback
        );

        $this->assertApiResponse($editedVatReturnFilledCategoryRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filled_category_refferedbacks/'.$vatReturnFilledCategoryRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filled_category_refferedbacks/'.$vatReturnFilledCategoryRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}
