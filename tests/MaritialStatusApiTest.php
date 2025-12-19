<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeMaritialStatusTrait;
use Tests\ApiTestTrait;

class MaritialStatusApiTest extends TestCase
{
    use MakeMaritialStatusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_maritial_status()
    {
        $maritialStatus = $this->fakeMaritialStatusData();
        $this->response = $this->json('POST', '/api/maritialStatuses', $maritialStatus);

        $this->assertApiResponse($maritialStatus);
    }

    /**
     * @test
     */
    public function test_read_maritial_status()
    {
        $maritialStatus = $this->makeMaritialStatus();
        $this->response = $this->json('GET', '/api/maritialStatuses/'.$maritialStatus->id);

        $this->assertApiResponse($maritialStatus->toArray());
    }

    /**
     * @test
     */
    public function test_update_maritial_status()
    {
        $maritialStatus = $this->makeMaritialStatus();
        $editedMaritialStatus = $this->fakeMaritialStatusData();

        $this->response = $this->json('PUT', '/api/maritialStatuses/'.$maritialStatus->id, $editedMaritialStatus);

        $this->assertApiResponse($editedMaritialStatus);
    }

    /**
     * @test
     */
    public function test_delete_maritial_status()
    {
        $maritialStatus = $this->makeMaritialStatus();
        $this->response = $this->json('DELETE', '/api/maritialStatuses/'.$maritialStatus->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/maritialStatuses/'.$maritialStatus->id);

        $this->response->assertStatus(404);
    }
}
