<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisteredSupplier;

class RegisteredSupplierApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/registered_suppliers', $registeredSupplier
        );

        $this->assertApiResponse($registeredSupplier);
    }

    /**
     * @test
     */
    public function test_read_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/registered_suppliers/'.$registeredSupplier->id
        );

        $this->assertApiResponse($registeredSupplier->toArray());
    }

    /**
     * @test
     */
    public function test_update_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->create();
        $editedRegisteredSupplier = factory(RegisteredSupplier::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/registered_suppliers/'.$registeredSupplier->id,
            $editedRegisteredSupplier
        );

        $this->assertApiResponse($editedRegisteredSupplier);
    }

    /**
     * @test
     */
    public function test_delete_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/registered_suppliers/'.$registeredSupplier->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/registered_suppliers/'.$registeredSupplier->id
        );

        $this->response->assertStatus(404);
    }
}
