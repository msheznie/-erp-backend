<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisteredBankMemoSupplier;

class RegisteredBankMemoSupplierApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/registered_bank_memo_suppliers', $registeredBankMemoSupplier
        );

        $this->assertApiResponse($registeredBankMemoSupplier);
    }

    /**
     * @test
     */
    public function test_read_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/registered_bank_memo_suppliers/'.$registeredBankMemoSupplier->id
        );

        $this->assertApiResponse($registeredBankMemoSupplier->toArray());
    }

    /**
     * @test
     */
    public function test_update_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->create();
        $editedRegisteredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/registered_bank_memo_suppliers/'.$registeredBankMemoSupplier->id,
            $editedRegisteredBankMemoSupplier
        );

        $this->assertApiResponse($editedRegisteredBankMemoSupplier);
    }

    /**
     * @test
     */
    public function test_delete_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/registered_bank_memo_suppliers/'.$registeredBankMemoSupplier->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/registered_bank_memo_suppliers/'.$registeredBankMemoSupplier->id
        );

        $this->response->assertStatus(404);
    }
}
