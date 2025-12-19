<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RegisteredSupplierAttachment;

class RegisteredSupplierAttachmentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/registered_supplier_attachments', $registeredSupplierAttachment
        );

        $this->assertApiResponse($registeredSupplierAttachment);
    }

    /**
     * @test
     */
    public function test_read_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/registered_supplier_attachments/'.$registeredSupplierAttachment->id
        );

        $this->assertApiResponse($registeredSupplierAttachment->toArray());
    }

    /**
     * @test
     */
    public function test_update_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->create();
        $editedRegisteredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/registered_supplier_attachments/'.$registeredSupplierAttachment->id,
            $editedRegisteredSupplierAttachment
        );

        $this->assertApiResponse($editedRegisteredSupplierAttachment);
    }

    /**
     * @test
     */
    public function test_delete_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/registered_supplier_attachments/'.$registeredSupplierAttachment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/registered_supplier_attachments/'.$registeredSupplierAttachment->id
        );

        $this->response->assertStatus(404);
    }
}
