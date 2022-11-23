<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SourceCustomerTypeMaster;

class SourceCustomerTypeMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/source_customer_type_masters', $sourceCustomerTypeMaster
        );

        $this->assertApiResponse($sourceCustomerTypeMaster);
    }

    /**
     * @test
     */
    public function test_read_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/source_customer_type_masters/'.$sourceCustomerTypeMaster->id
        );

        $this->assertApiResponse($sourceCustomerTypeMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->create();
        $editedSourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/source_customer_type_masters/'.$sourceCustomerTypeMaster->id,
            $editedSourceCustomerTypeMaster
        );

        $this->assertApiResponse($editedSourceCustomerTypeMaster);
    }

    /**
     * @test
     */
    public function test_delete_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/source_customer_type_masters/'.$sourceCustomerTypeMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/source_customer_type_masters/'.$sourceCustomerTypeMaster->id
        );

        $this->response->assertStatus(404);
    }
}
