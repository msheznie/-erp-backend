<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeReligionTrait;
use Tests\ApiTestTrait;

class ReligionApiTest extends TestCase
{
    use MakeReligionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_religion()
    {
        $religion = $this->fakeReligionData();
        $this->response = $this->json('POST', '/api/religions', $religion);

        $this->assertApiResponse($religion);
    }

    /**
     * @test
     */
    public function test_read_religion()
    {
        $religion = $this->makeReligion();
        $this->response = $this->json('GET', '/api/religions/'.$religion->id);

        $this->assertApiResponse($religion->toArray());
    }

    /**
     * @test
     */
    public function test_update_religion()
    {
        $religion = $this->makeReligion();
        $editedReligion = $this->fakeReligionData();

        $this->response = $this->json('PUT', '/api/religions/'.$religion->id, $editedReligion);

        $this->assertApiResponse($editedReligion);
    }

    /**
     * @test
     */
    public function test_delete_religion()
    {
        $religion = $this->makeReligion();
        $this->response = $this->json('DELETE', '/api/religions/'.$religion->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/religions/'.$religion->id);

        $this->response->assertStatus(404);
    }
}
