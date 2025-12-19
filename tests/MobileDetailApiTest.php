<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MobileDetail;

class MobileDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mobile_details', $mobileDetail
        );

        $this->assertApiResponse($mobileDetail);
    }

    /**
     * @test
     */
    public function test_read_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mobile_details/'.$mobileDetail->id
        );

        $this->assertApiResponse($mobileDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->create();
        $editedMobileDetail = factory(MobileDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mobile_details/'.$mobileDetail->id,
            $editedMobileDetail
        );

        $this->assertApiResponse($editedMobileDetail);
    }

    /**
     * @test
     */
    public function test_delete_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mobile_details/'.$mobileDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mobile_details/'.$mobileDetail->id
        );

        $this->response->assertStatus(404);
    }
}
