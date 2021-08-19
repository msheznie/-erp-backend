<?php namespace Tests\Repositories;

use App\Models\NotificationCompanyScenario;
use App\Repositories\NotificationCompanyScenarioRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NotificationCompanyScenarioRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationCompanyScenarioRepository
     */
    protected $notificationCompanyScenarioRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->notificationCompanyScenarioRepo = \App::make(NotificationCompanyScenarioRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->make()->toArray();

        $createdNotificationCompanyScenario = $this->notificationCompanyScenarioRepo->create($notificationCompanyScenario);

        $createdNotificationCompanyScenario = $createdNotificationCompanyScenario->toArray();
        $this->assertArrayHasKey('id', $createdNotificationCompanyScenario);
        $this->assertNotNull($createdNotificationCompanyScenario['id'], 'Created NotificationCompanyScenario must have id specified');
        $this->assertNotNull(NotificationCompanyScenario::find($createdNotificationCompanyScenario['id']), 'NotificationCompanyScenario with given id must be in DB');
        $this->assertModelData($notificationCompanyScenario, $createdNotificationCompanyScenario);
    }

    /**
     * @test read
     */
    public function test_read_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->create();

        $dbNotificationCompanyScenario = $this->notificationCompanyScenarioRepo->find($notificationCompanyScenario->id);

        $dbNotificationCompanyScenario = $dbNotificationCompanyScenario->toArray();
        $this->assertModelData($notificationCompanyScenario->toArray(), $dbNotificationCompanyScenario);
    }

    /**
     * @test update
     */
    public function test_update_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->create();
        $fakeNotificationCompanyScenario = factory(NotificationCompanyScenario::class)->make()->toArray();

        $updatedNotificationCompanyScenario = $this->notificationCompanyScenarioRepo->update($fakeNotificationCompanyScenario, $notificationCompanyScenario->id);

        $this->assertModelData($fakeNotificationCompanyScenario, $updatedNotificationCompanyScenario->toArray());
        $dbNotificationCompanyScenario = $this->notificationCompanyScenarioRepo->find($notificationCompanyScenario->id);
        $this->assertModelData($fakeNotificationCompanyScenario, $dbNotificationCompanyScenario->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_notification_company_scenario()
    {
        $notificationCompanyScenario = factory(NotificationCompanyScenario::class)->create();

        $resp = $this->notificationCompanyScenarioRepo->delete($notificationCompanyScenario->id);

        $this->assertTrue($resp);
        $this->assertNull(NotificationCompanyScenario::find($notificationCompanyScenario->id), 'NotificationCompanyScenario should not exist in DB');
    }
}
