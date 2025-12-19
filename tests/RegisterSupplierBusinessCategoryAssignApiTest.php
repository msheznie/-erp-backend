<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisterSupplierBusinessCategoryAssign;

class RegisterSupplierBusinessCategoryAssignApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/register_supplier_business_category_assigns', $registerSupplierBusinessCategoryAssign
        );

        $this->assertApiResponse($registerSupplierBusinessCategoryAssign);
    }

    /**
     * @test
     */
    public function test_read_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/register_supplier_business_category_assigns/'.$registerSupplierBusinessCategoryAssign->id
        );

        $this->assertApiResponse($registerSupplierBusinessCategoryAssign->toArray());
    }

    /**
     * @test
     */
    public function test_update_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->create();
        $editedRegisterSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/register_supplier_business_category_assigns/'.$registerSupplierBusinessCategoryAssign->id,
            $editedRegisterSupplierBusinessCategoryAssign
        );

        $this->assertApiResponse($editedRegisterSupplierBusinessCategoryAssign);
    }

    /**
     * @test
     */
    public function test_delete_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/register_supplier_business_category_assigns/'.$registerSupplierBusinessCategoryAssign->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/register_supplier_business_category_assigns/'.$registerSupplierBusinessCategoryAssign->id
        );

        $this->response->assertStatus(404);
    }
}
