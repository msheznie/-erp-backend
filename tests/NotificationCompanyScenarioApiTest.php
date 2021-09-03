<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\NotificationCompanyScenario;

class NotificationCompanyScenarioApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/notification_company_scenarios', $notificationCompanyScenario
        );

        $this->assertApiResponse($notificationCompanyScenario);
    }

    /**
     * @test
     */
    public function test_read_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/notification_company_scenarios/'.$notificationCompanyScenario->id
        );

        $this->assertApiResponse($notificationCompanyScenario->toArray());
    }

    /**
     * @test
     */
    public function test_update_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->create();
        $editedNotificationCompanyScenario = factory(NotificationCompanyScenario::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/notification_company_scenarios/'.$notificationCompanyScenario->id,
            $editedNotificationCompanyScenario
        );

        $this->assertApiResponse($editedNotificationCompanyScenario);
    }

    /**
     * @test
     */
    public function test_delete_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/notification_company_scenarios/'.$notificationCompanyScenario->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/notification_company_scenarios/'.$notificationCompanyScenario->id
        );

        $this->response->assertStatus(404);
    }
}
