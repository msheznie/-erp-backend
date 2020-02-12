<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeClientPerformaAppTypeTrait;
use Tests\ApiTestTrait;

class ClientPerformaAppTypeApiTest extends TestCase
{
    use MakeClientPerformaAppTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_client_performa_app_type()
    {
        $clientPerformaAppType = $this->fakeClientPerformaAppTypeData();
        $this->response = $this->json('POST', '/api/clientPerformaAppTypes', $clientPerformaAppType);

        $this->assertApiResponse($clientPerformaAppType);
    }

    /**
     * @test
     */
    public function test_read_client_performa_app_type()
    {
        $clientPerformaAppType = $this->makeClientPerformaAppType();
        $this->response = $this->json('GET', '/api/clientPerformaAppTypes/'.$clientPerformaAppType->id);

        $this->assertApiResponse($clientPerformaAppType->toArray());
    }

    /**
     * @test
     */
    public function test_update_client_performa_app_type()
    {
        $clientPerformaAppType = $this->makeClientPerformaAppType();
        $editedClientPerformaAppType = $this->fakeClientPerformaAppTypeData();

        $this->response = $this->json('PUT', '/api/clientPerformaAppTypes/'.$clientPerformaAppType->id, $editedClientPerformaAppType);

        $this->assertApiResponse($editedClientPerformaAppType);
    }

    /**
     * @test
     */
    public function test_delete_client_performa_app_type()
    {
        $clientPerformaAppType = $this->makeClientPerformaAppType();
        $this->response = $this->json('DELETE', '/api/clientPerformaAppTypes/'.$clientPerformaAppType->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/clientPerformaAppTypes/'.$clientPerformaAppType->id);

        $this->response->assertStatus(404);
    }
}
