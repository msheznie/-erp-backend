<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HRFeatureFlags;

class HRFeatureFlagsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/h_r_feature_flags', $hRFeatureFlags
        );

        $this->assertApiResponse($hRFeatureFlags);
    }

    /**
     * @test
     */
    public function test_read_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/h_r_feature_flags/'.$hRFeatureFlags->id
        );

        $this->assertApiResponse($hRFeatureFlags->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->create();
        $editedHRFeatureFlags = factory(HRFeatureFlags::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/h_r_feature_flags/'.$hRFeatureFlags->id,
            $editedHRFeatureFlags
        );

        $this->assertApiResponse($editedHRFeatureFlags);
    }

    /**
     * @test
     */
    public function test_delete_h_r_feature_flags()
    {
        $hRFeatureFlags = factory(HRFeatureFlags::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/h_r_feature_flags/'.$hRFeatureFlags->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/h_r_feature_flags/'.$hRFeatureFlags->id
        );

        $this->response->assertStatus(404);
    }
}
