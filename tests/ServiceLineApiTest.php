<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeServiceLineTrait;
use Tests\ApiTestTrait;

class ServiceLineApiTest extends TestCase
{
    use MakeServiceLineTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_service_line()
    {
        $serviceLine = $this->fakeServiceLineData();
        $this->response = $this->json('POST', '/api/serviceLines', $serviceLine);

        $this->assertApiResponse($serviceLine);
    }

    /**
     * @test
     */
    public function test_read_service_line()
    {
        $serviceLine = $this->makeServiceLine();
        $this->response = $this->json('GET', '/api/serviceLines/'.$serviceLine->id);

        $this->assertApiResponse($serviceLine->toArray());
    }

    /**
     * @test
     */
    public function test_update_service_line()
    {
        $serviceLine = $this->makeServiceLine();
        $editedServiceLine = $this->fakeServiceLineData();

        $this->response = $this->json('PUT', '/api/serviceLines/'.$serviceLine->id, $editedServiceLine);

        $this->assertApiResponse($editedServiceLine);
    }

    /**
     * @test
     */
    public function test_delete_service_line()
    {
        $serviceLine = $this->makeServiceLine();
        $this->response = $this->json('DELETE', '/api/serviceLines/'.$serviceLine->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/serviceLines/'.$serviceLine->id);

        $this->response->assertStatus(404);
    }
}
