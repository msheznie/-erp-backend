<?php namespace Tests\Repositories;

use App\Models\NotificationScenarios;
use App\Repositories\NotificationScenariosRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NotificationScenariosRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationScenariosRepository
     */
    protected $notificationScenariosRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->notificationScenariosRepo = \App::make(NotificationScenariosRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->make()->toArray();

        $createdNotificationScenarios = $this->notificationScenariosRepo->create($notificationScenarios);

        $createdNotificationScenarios = $createdNotificationScenarios->toArray();
        $this->assertArrayHasKey('id', $createdNotificationScenarios);
        $this->assertNotNull($createdNotificationScenarios['id'], 'Created NotificationScenarios must have id specified');
        $this->assertNotNull(NotificationScenarios::find($createdNotificationScenarios['id']), 'NotificationScenarios with given id must be in DB');
        $this->assertModelData($notificationScenarios, $createdNotificationScenarios);
    }

    /**
     * @test read
     */
    public function test_read_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->create();

        $dbNotificationScenarios = $this->notificationScenariosRepo->find($notificationScenarios->id);

        $dbNotificationScenarios = $dbNotificationScenarios->toArray();
        $this->assertModelData($notificationScenarios->toArray(), $dbNotificationScenarios);
    }

    /**
     * @test update
     */
    public function test_update_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->create();
        $fakeNotificationScenarios = factory(NotificationScenarios::class)->make()->toArray();

        $updatedNotificationScenarios = $this->notificationScenariosRepo->update($fakeNotificationScenarios, $notificationScenarios->id);

        $this->assertModelData($fakeNotificationScenarios, $updatedNotificationScenarios->toArray());
        $dbNotificationScenarios = $this->notificationScenariosRepo->find($notificationScenarios->id);
        $this->assertModelData($fakeNotificationScenarios, $dbNotificationScenarios->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_notification_scenarios()
    {
        $notificationScenarios = factory(NotificationScenarios::class)->create();

        $resp = $this->notificationScenariosRepo->delete($notificationScenarios->id);

        $this->assertTrue($resp);
        $this->assertNull(NotificationScenarios::find($notificationScenarios->id), 'NotificationScenarios should not exist in DB');
    }
}
