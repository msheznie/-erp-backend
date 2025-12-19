<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FcmToken;

class FcmTokenApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/fcm_tokens', $fcmToken
        );

        $this->assertApiResponse($fcmToken);
    }

    /**
     * @test
     */
    public function test_read_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/fcm_tokens/'.$fcmToken->id
        );

        $this->assertApiResponse($fcmToken->toArray());
    }

    /**
     * @test
     */
    public function test_update_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->create();
        $editedFcmToken = factory(FcmToken::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/fcm_tokens/'.$fcmToken->id,
            $editedFcmToken
        );

        $this->assertApiResponse($editedFcmToken);
    }

    /**
     * @test
     */
    public function test_delete_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/fcm_tokens/'.$fcmToken->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/fcm_tokens/'.$fcmToken->id
        );

        $this->response->assertStatus(404);
    }
}
