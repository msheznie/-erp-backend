<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrDeligationDetails;

class HrDeligationDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_deligation_details', $hrDeligationDetails
        );

        $this->assertApiResponse($hrDeligationDetails);
    }

    /**
     * @test
     */
    public function test_read_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_deligation_details/'.$hrDeligationDetails->id
        );

        $this->assertApiResponse($hrDeligationDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->create();
        $editedHrDeligationDetails = factory(HrDeligationDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_deligation_details/'.$hrDeligationDetails->id,
            $editedHrDeligationDetails
        );

        $this->assertApiResponse($editedHrDeligationDetails);
    }

    /**
     * @test
     */
    public function test_delete_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_deligation_details/'.$hrDeligationDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_deligation_details/'.$hrDeligationDetails->id
        );

        $this->response->assertStatus(404);
    }
}
