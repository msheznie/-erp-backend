<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeGenderTrait;
use Tests\ApiTestTrait;

class GenderApiTest extends TestCase
{
    use MakeGenderTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_gender()
    {
        $gender = $this->fakeGenderData();
        $this->response = $this->json('POST', '/api/genders', $gender);

        $this->assertApiResponse($gender);
    }

    /**
     * @test
     */
    public function test_read_gender()
    {
        $gender = $this->makeGender();
        $this->response = $this->json('GET', '/api/genders/'.$gender->id);

        $this->assertApiResponse($gender->toArray());
    }

    /**
     * @test
     */
    public function test_update_gender()
    {
        $gender = $this->makeGender();
        $editedGender = $this->fakeGenderData();

        $this->response = $this->json('PUT', '/api/genders/'.$gender->id, $editedGender);

        $this->assertApiResponse($editedGender);
    }

    /**
     * @test
     */
    public function test_delete_gender()
    {
        $gender = $this->makeGender();
        $this->response = $this->json('DELETE', '/api/genders/'.$gender->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/genders/'.$gender->id);

        $this->response->assertStatus(404);
    }
}
