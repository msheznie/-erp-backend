<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomReportMaster;

class CustomReportMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_report_masters', $customReportMaster
        );

        $this->assertApiResponse($customReportMaster);
    }

    /**
     * @test
     */
    public function test_read_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_report_masters/'.$customReportMaster->id
        );

        $this->assertApiResponse($customReportMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->create();
        $editedCustomReportMaster = factory(CustomReportMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_report_masters/'.$customReportMaster->id,
            $editedCustomReportMaster
        );

        $this->assertApiResponse($editedCustomReportMaster);
    }

    /**
     * @test
     */
    public function test_delete_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_report_masters/'.$customReportMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_report_masters/'.$customReportMaster->id
        );

        $this->response->assertStatus(404);
    }
}
