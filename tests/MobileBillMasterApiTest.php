<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MobileBillMaster;

class MobileBillMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mobile_bill_masters', $mobileBillMaster
        );

        $this->assertApiResponse($mobileBillMaster);
    }

    /**
     * @test
     */
    public function test_read_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mobile_bill_masters/'.$mobileBillMaster->id
        );

        $this->assertApiResponse($mobileBillMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->create();
        $editedMobileBillMaster = factory(MobileBillMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mobile_bill_masters/'.$mobileBillMaster->id,
            $editedMobileBillMaster
        );

        $this->assertApiResponse($editedMobileBillMaster);
    }

    /**
     * @test
     */
    public function test_delete_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mobile_bill_masters/'.$mobileBillMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mobile_bill_masters/'.$mobileBillMaster->id
        );

        $this->response->assertStatus(404);
    }
}
