<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisteredSupplierCurrency;

class RegisteredSupplierCurrencyApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/registered_supplier_currencies', $registeredSupplierCurrency
        );

        $this->assertApiResponse($registeredSupplierCurrency);
    }

    /**
     * @test
     */
    public function test_read_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/registered_supplier_currencies/'.$registeredSupplierCurrency->id
        );

        $this->assertApiResponse($registeredSupplierCurrency->toArray());
    }

    /**
     * @test
     */
    public function test_update_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->create();
        $editedRegisteredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/registered_supplier_currencies/'.$registeredSupplierCurrency->id,
            $editedRegisteredSupplierCurrency
        );

        $this->assertApiResponse($editedRegisteredSupplierCurrency);
    }

    /**
     * @test
     */
    public function test_delete_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/registered_supplier_currencies/'.$registeredSupplierCurrency->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/registered_supplier_currencies/'.$registeredSupplierCurrency->id
        );

        $this->response->assertStatus(404);
    }
}
