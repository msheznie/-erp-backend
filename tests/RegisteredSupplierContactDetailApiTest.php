<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisteredSupplierContactDetail;

class RegisteredSupplierContactDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/registered_supplier_contact_details', $registeredSupplierContactDetail
        );

        $this->assertApiResponse($registeredSupplierContactDetail);
    }

    /**
     * @test
     */
    public function test_read_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/registered_supplier_contact_details/'.$registeredSupplierContactDetail->id
        );

        $this->assertApiResponse($registeredSupplierContactDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->create();
        $editedRegisteredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/registered_supplier_contact_details/'.$registeredSupplierContactDetail->id,
            $editedRegisteredSupplierContactDetail
        );

        $this->assertApiResponse($editedRegisteredSupplierContactDetail);
    }

    /**
     * @test
     */
    public function test_delete_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/registered_supplier_contact_details/'.$registeredSupplierContactDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/registered_supplier_contact_details/'.$registeredSupplierContactDetail->id
        );

        $this->response->assertStatus(404);
    }
}
