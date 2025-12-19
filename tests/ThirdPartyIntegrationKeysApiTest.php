<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ThirdPartyIntegrationKeys;

class ThirdPartyIntegrationKeysApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/third_party_integration_keys', $thirdPartyIntegrationKeys
        );

        $this->assertApiResponse($thirdPartyIntegrationKeys);
    }

    /**
     * @test
     */
    public function test_read_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/third_party_integration_keys/'.$thirdPartyIntegrationKeys->id
        );

        $this->assertApiResponse($thirdPartyIntegrationKeys->toArray());
    }

    /**
     * @test
     */
    public function test_update_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->create();
        $editedThirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/third_party_integration_keys/'.$thirdPartyIntegrationKeys->id,
            $editedThirdPartyIntegrationKeys
        );

        $this->assertApiResponse($editedThirdPartyIntegrationKeys);
    }

    /**
     * @test
     */
    public function test_delete_third_party_integration_keys()
    {
        $thirdPartyIntegrationKeys = factory(ThirdPartyIntegrationKeys::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/third_party_integration_keys/'.$thirdPartyIntegrationKeys->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/third_party_integration_keys/'.$thirdPartyIntegrationKeys->id
        );

        $this->response->assertStatus(404);
    }
}
