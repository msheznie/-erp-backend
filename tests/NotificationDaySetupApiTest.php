<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\NotificationDaySetup;

class NotificationDaySetupApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/notification_day_setups', $notificationDaySetup
        );

        $this->assertApiResponse($notificationDaySetup);
    }

    /**
     * @test
     */
    public function test_read_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/notification_day_setups/'.$notificationDaySetup->id
        );

        $this->assertApiResponse($notificationDaySetup->toArray());
    }

    /**
     * @test
     */
    public function test_update_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->create();
        $editedNotificationDaySetup = factory(NotificationDaySetup::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/notification_day_setups/'.$notificationDaySetup->id,
            $editedNotificationDaySetup
        );

        $this->assertApiResponse($editedNotificationDaySetup);
    }

    /**
     * @test
     */
    public function test_delete_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/notification_day_setups/'.$notificationDaySetup->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/notification_day_setups/'.$notificationDaySetup->id
        );

        $this->response->assertStatus(404);
    }
}
