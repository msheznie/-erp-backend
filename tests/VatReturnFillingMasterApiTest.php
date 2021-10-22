<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFillingMaster;

class VatReturnFillingMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filling_masters', $vatReturnFillingMaster
        );

        $this->assertApiResponse($vatReturnFillingMaster);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_masters/'.$vatReturnFillingMaster->id
        );

        $this->assertApiResponse($vatReturnFillingMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->create();
        $editedVatReturnFillingMaster = factory(VatReturnFillingMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filling_masters/'.$vatReturnFillingMaster->id,
            $editedVatReturnFillingMaster
        );

        $this->assertApiResponse($editedVatReturnFillingMaster);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filling_masters/'.$vatReturnFillingMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_masters/'.$vatReturnFillingMaster->id
        );

        $this->response->assertStatus(404);
    }
}
