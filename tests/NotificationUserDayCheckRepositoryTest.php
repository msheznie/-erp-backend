<?php namespace Tests\Repositories;

use App\Models\NotificationUserDayCheck;
use App\Repositories\NotificationUserDayCheckRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NotificationUserDayCheckRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationUserDayCheckRepository
     */
    protected $notificationUserDayCheckRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->notificationUserDayCheckRepo = \App::make(NotificationUserDayCheckRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->make()->toArray();

        $createdNotificationUserDayCheck = $this->notificationUserDayCheckRepo->create($notificationUserDayCheck);

        $createdNotificationUserDayCheck = $createdNotificationUserDayCheck->toArray();
        $this->assertArrayHasKey('id', $createdNotificationUserDayCheck);
        $this->assertNotNull($createdNotificationUserDayCheck['id'], 'Created NotificationUserDayCheck must have id specified');
        $this->assertNotNull(NotificationUserDayCheck::find($createdNotificationUserDayCheck['id']), 'NotificationUserDayCheck with given id must be in DB');
        $this->assertModelData($notificationUserDayCheck, $createdNotificationUserDayCheck);
    }

    /**
     * @test read
     */
    public function test_read_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->create();

        $dbNotificationUserDayCheck = $this->notificationUserDayCheckRepo->find($notificationUserDayCheck->id);

        $dbNotificationUserDayCheck = $dbNotificationUserDayCheck->toArray();
        $this->assertModelData($notificationUserDayCheck->toArray(), $dbNotificationUserDayCheck);
    }

    /**
     * @test update
     */
    public function test_update_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->create();
        $fakeNotificationUserDayCheck = factory(NotificationUserDayCheck::class)->make()->toArray();

        $updatedNotificationUserDayCheck = $this->notificationUserDayCheckRepo->update($fakeNotificationUserDayCheck, $notificationUserDayCheck->id);

        $this->assertModelData($fakeNotificationUserDayCheck, $updatedNotificationUserDayCheck->toArray());
        $dbNotificationUserDayCheck = $this->notificationUserDayCheckRepo->find($notificationUserDayCheck->id);
        $this->assertModelData($fakeNotificationUserDayCheck, $dbNotificationUserDayCheck->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_notification_user_day_check()
    {
        $notificationUserDayCheck = factory(NotificationUserDayCheck::class)->create();

        $resp = $this->notificationUserDayCheckRepo->delete($notificationUserDayCheck->id);

        $this->assertTrue($resp);
        $this->assertNull(NotificationUserDayCheck::find($notificationUserDayCheck->id), 'NotificationUserDayCheck should not exist in DB');
    }
}
