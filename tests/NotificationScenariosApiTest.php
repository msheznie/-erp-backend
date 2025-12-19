<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\NotificationScenarios;

class NotificationScenariosApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/notification_scenarios', $notificationScenarios
        );

        $this->assertApiResponse($notificationScenarios);
    }

    /**
     * @test
     */
    public function test_read_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/notification_scenarios/'.$notificationScenarios->id
        );

        $this->assertApiResponse($notificationScenarios->toArray());
    }

    /**
     * @test
     */
    public function test_update_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->create();
        $editedNotificationScenarios = factory(NotificationScenarios::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/notification_scenarios/'.$notificationScenarios->id,
            $editedNotificationScenarios
        );

        $this->assertApiResponse($editedNotificationScenarios);
    }

    /**
     * @test
     */
    public function test_delete_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/notification_scenarios/'.$notificationScenarios->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/notification_scenarios/'.$notificationScenarios->id
        );

        $this->response->assertStatus(404);
    }
}
