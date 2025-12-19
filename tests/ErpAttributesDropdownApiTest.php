<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpAttributesDropdown;

class ErpAttributesDropdownApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_attributes_dropdowns', $erpAttributesDropdown
        );

        $this->assertApiResponse($erpAttributesDropdown);
    }

    /**
     * @test
     */
    public function test_read_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_attributes_dropdowns/'.$erpAttributesDropdown->id
        );

        $this->assertApiResponse($erpAttributesDropdown->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->create();
        $editedErpAttributesDropdown = factory(ErpAttributesDropdown::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_attributes_dropdowns/'.$erpAttributesDropdown->id,
            $editedErpAttributesDropdown
        );

        $this->assertApiResponse($editedErpAttributesDropdown);
    }

    /**
     * @test
     */
    public function test_delete_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_attributes_dropdowns/'.$erpAttributesDropdown->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_attributes_dropdowns/'.$erpAttributesDropdown->id
        );

        $this->response->assertStatus(404);
    }
}
