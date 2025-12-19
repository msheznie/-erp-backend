<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TaxMaster;

class TaxMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tax_masters', $taxMaster
        );

        $this->assertApiResponse($taxMaster);
    }

    /**
     * @test
     */
    public function test_read_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tax_masters/'.$taxMaster->id
        );

        $this->assertApiResponse($taxMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->create();
        $editedTaxMaster = factory(TaxMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tax_masters/'.$taxMaster->id,
            $editedTaxMaster
        );

        $this->assertApiResponse($editedTaxMaster);
    }

    /**
     * @test
     */
    public function test_delete_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tax_masters/'.$taxMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tax_masters/'.$taxMaster->id
        );

        $this->response->assertStatus(404);
    }
}
