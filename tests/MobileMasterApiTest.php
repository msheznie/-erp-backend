<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MobileMaster;

class MobileMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mobile_masters', $mobileMaster
        );

        $this->assertApiResponse($mobileMaster);
    }

    /**
     * @test
     */
    public function test_read_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mobile_masters/'.$mobileMaster->id
        );

        $this->assertApiResponse($mobileMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->create();
        $editedMobileMaster = factory(MobileMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mobile_masters/'.$mobileMaster->id,
            $editedMobileMaster
        );

        $this->assertApiResponse($editedMobileMaster);
    }

    /**
     * @test
     */
    public function test_delete_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mobile_masters/'.$mobileMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mobile_masters/'.$mobileMaster->id
        );

        $this->response->assertStatus(404);
    }
}
