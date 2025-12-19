<?php namespace Tests\Repositories;

use App\Models\NotificationDaySetup;
use App\Repositories\NotificationDaySetupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NotificationDaySetupRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationDaySetupRepository
     */
    protected $notificationDaySetupRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->notificationDaySetupRepo = \App::make(NotificationDaySetupRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->make()->toArray();

        $createdNotificationDaySetup = $this->notificationDaySetupRepo->create($notificationDaySetup);

        $createdNotificationDaySetup = $createdNotificationDaySetup->toArray();
        $this->assertArrayHasKey('id', $createdNotificationDaySetup);
        $this->assertNotNull($createdNotificationDaySetup['id'], 'Created NotificationDaySetup must have id specified');
        $this->assertNotNull(NotificationDaySetup::find($createdNotificationDaySetup['id']), 'NotificationDaySetup with given id must be in DB');
        $this->assertModelData($notificationDaySetup, $createdNotificationDaySetup);
    }

    /**
     * @test read
     */
    public function test_read_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->create();

        $dbNotificationDaySetup = $this->notificationDaySetupRepo->find($notificationDaySetup->id);

        $dbNotificationDaySetup = $dbNotificationDaySetup->toArray();
        $this->assertModelData($notificationDaySetup->toArray(), $dbNotificationDaySetup);
    }

    /**
     * @test update
     */
    public function test_update_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->create();
        $fakeNotificationDaySetup = factory(NotificationDaySetup::class)->make()->toArray();

        $updatedNotificationDaySetup = $this->notificationDaySetupRepo->update($fakeNotificationDaySetup, $notificationDaySetup->id);

        $this->assertModelData($fakeNotificationDaySetup, $updatedNotificationDaySetup->toArray());
        $dbNotificationDaySetup = $this->notificationDaySetupRepo->find($notificationDaySetup->id);
        $this->assertModelData($fakeNotificationDaySetup, $dbNotificationDaySetup->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_notification_day_setup()
    {
        $notificationDaySetup = factory(NotificationDaySetup::class)->create();

        $resp = $this->notificationDaySetupRepo->delete($notificationDaySetup->id);

        $this->assertTrue($resp);
        $this->assertNull(NotificationDaySetup::find($notificationDaySetup->id), 'NotificationDaySetup should not exist in DB');
    }
}
