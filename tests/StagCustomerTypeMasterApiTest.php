<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\StagCustomerTypeMaster;

class StagCustomerTypeMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/stag_customer_type_masters', $stagCustomerTypeMaster
        );

        $this->assertApiResponse($stagCustomerTypeMaster);
    }

    /**
     * @test
     */
    public function test_read_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/stag_customer_type_masters/'.$stagCustomerTypeMaster->id
        );

        $this->assertApiResponse($stagCustomerTypeMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->create();
        $editedStagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/stag_customer_type_masters/'.$stagCustomerTypeMaster->id,
            $editedStagCustomerTypeMaster
        );

        $this->assertApiResponse($editedStagCustomerTypeMaster);
    }

    /**
     * @test
     */
    public function test_delete_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/stag_customer_type_masters/'.$stagCustomerTypeMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/stag_customer_type_masters/'.$stagCustomerTypeMaster->id
        );

        $this->response->assertStatus(404);
    }
}
