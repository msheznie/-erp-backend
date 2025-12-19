<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ThirdPartyDomain;

class ThirdPartyDomainApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/third_party_domains', $thirdPartyDomain
        );

        $this->assertApiResponse($thirdPartyDomain);
    }

    /**
     * @test
     */
    public function test_read_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/third_party_domains/'.$thirdPartyDomain->id
        );

        $this->assertApiResponse($thirdPartyDomain->toArray());
    }

    /**
     * @test
     */
    public function test_update_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->create();
        $editedThirdPartyDomain = factory(ThirdPartyDomain::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/third_party_domains/'.$thirdPartyDomain->id,
            $editedThirdPartyDomain
        );

        $this->assertApiResponse($editedThirdPartyDomain);
    }

    /**
     * @test
     */
    public function test_delete_third_party_domain()
    {
        $thirdPartyDomain = factory(ThirdPartyDomain::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/third_party_domains/'.$thirdPartyDomain->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/third_party_domains/'.$thirdPartyDomain->id
        );

        $this->response->assertStatus(404);
    }
}
