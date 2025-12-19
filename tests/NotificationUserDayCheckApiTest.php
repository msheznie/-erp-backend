<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\NotificationUserDayCheck;

class NotificationUserDayCheckApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/notification_user_day_checks', $notificationUserDayCheck
        );

        $this->assertApiResponse($notificationUserDayCheck);
    }

    /**
     * @test
     */
    public function test_read_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/notification_user_day_checks/'.$notificationUserDayCheck->id
        );

        $this->assertApiResponse($notificationUserDayCheck->toArray());
    }

    /**
     * @test
     */
    public function test_update_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->create();
        $editedNotificationUserDayCheck = factory(NotificationUserDayCheck::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/notification_user_day_checks/'.$notificationUserDayCheck->id,
            $editedNotificationUserDayCheck
        );

        $this->assertApiResponse($editedNotificationUserDayCheck);
    }

    /**
     * @test
     */
    public function test_delete_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/notification_user_day_checks/'.$notificationUserDayCheck->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/notification_user_day_checks/'.$notificationUserDayCheck->id
        );

        $this->response->assertStatus(404);
    }
}
