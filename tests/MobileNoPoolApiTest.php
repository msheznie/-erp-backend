<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MobileNoPool;

class MobileNoPoolApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mobile_no_pools', $mobileNoPool
        );

        $this->assertApiResponse($mobileNoPool);
    }

    /**
     * @test
     */
    public function test_read_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mobile_no_pools/'.$mobileNoPool->id
        );

        $this->assertApiResponse($mobileNoPool->toArray());
    }

    /**
     * @test
     */
    public function test_update_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->create();
        $editedMobileNoPool = factory(MobileNoPool::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mobile_no_pools/'.$mobileNoPool->id,
            $editedMobileNoPool
        );

        $this->assertApiResponse($editedMobileNoPool);
    }

    /**
     * @test
     */
    public function test_delete_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mobile_no_pools/'.$mobileNoPool->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mobile_no_pools/'.$mobileNoPool->id
        );

        $this->response->assertStatus(404);
    }
}
