<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ThirdPartySystems;

class ThirdPartySystemsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/third_party_systems', $thirdPartySystems
        );

        $this->assertApiResponse($thirdPartySystems);
    }

    /**
     * @test
     */
    public function test_read_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/third_party_systems/'.$thirdPartySystems->id
        );

        $this->assertApiResponse($thirdPartySystems->toArray());
    }

    /**
     * @test
     */
    public function test_update_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->create();
        $editedThirdPartySystems = factory(ThirdPartySystems::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/third_party_systems/'.$thirdPartySystems->id,
            $editedThirdPartySystems
        );

        $this->assertApiResponse($editedThirdPartySystems);
    }

    /**
     * @test
     */
    public function test_delete_third_party_systems()
    {
        $thirdPartySystems = factory(ThirdPartySystems::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/third_party_systems/'.$thirdPartySystems->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/third_party_systems/'.$thirdPartySystems->id
        );

        $this->response->assertStatus(404);
    }
}
