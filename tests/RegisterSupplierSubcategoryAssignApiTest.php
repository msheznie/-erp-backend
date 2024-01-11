<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisterSupplierSubcategoryAssign;

class RegisterSupplierSubcategoryAssignApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/register_supplier_subcategory_assigns', $registerSupplierSubcategoryAssign
        );

        $this->assertApiResponse($registerSupplierSubcategoryAssign);
    }

    /**
     * @test
     */
    public function test_read_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/register_supplier_subcategory_assigns/'.$registerSupplierSubcategoryAssign->id
        );

        $this->assertApiResponse($registerSupplierSubcategoryAssign->toArray());
    }

    /**
     * @test
     */
    public function test_update_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->create();
        $editedRegisterSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/register_supplier_subcategory_assigns/'.$registerSupplierSubcategoryAssign->id,
            $editedRegisterSupplierSubcategoryAssign
        );

        $this->assertApiResponse($editedRegisterSupplierSubcategoryAssign);
    }

    /**
     * @test
     */
    public function test_delete_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/register_supplier_subcategory_assigns/'.$registerSupplierSubcategoryAssign->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/register_supplier_subcategory_assigns/'.$registerSupplierSubcategoryAssign->id
        );

        $this->response->assertStatus(404);
    }
}
