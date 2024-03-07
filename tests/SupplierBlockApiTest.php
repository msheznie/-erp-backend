<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierBlock;

class SupplierBlockApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_blocks', $supplierBlock
        );

        $this->assertApiResponse($supplierBlock);
    }

    /**
     * @test
     */
    public function test_read_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_blocks/'.$supplierBlock->id
        );

        $this->assertApiResponse($supplierBlock->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->create();
        $editedSupplierBlock = factory(SupplierBlock::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_blocks/'.$supplierBlock->id,
            $editedSupplierBlock
        );

        $this->assertApiResponse($editedSupplierBlock);
    }

    /**
     * @test
     */
    public function test_delete_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_blocks/'.$supplierBlock->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_blocks/'.$supplierBlock->id
        );

        $this->response->assertStatus(404);
    }
}
