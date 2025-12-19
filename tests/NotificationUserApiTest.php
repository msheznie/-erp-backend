<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\NotificationUser;

class NotificationUserApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/notification_users', $notificationUser
        );

        $this->assertApiResponse($notificationUser);
    }

    /**
     * @test
     */
    public function test_read_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/notification_users/'.$notificationUser->id
        );

        $this->assertApiResponse($notificationUser->toArray());
    }

    /**
     * @test
     */
    public function test_update_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->create();
        $editedNotificationUser = factory(NotificationUser::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/notification_users/'.$notificationUser->id,
            $editedNotificationUser
        );

        $this->assertApiResponse($editedNotificationUser);
    }

    /**
     * @test
     */
    public function test_delete_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/notification_users/'.$notificationUser->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/notification_users/'.$notificationUser->id
        );

        $this->response->assertStatus(404);
    }
}
