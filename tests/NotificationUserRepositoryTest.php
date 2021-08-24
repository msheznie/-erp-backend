<?php namespace Tests\Repositories;

use App\Models\NotificationUser;
use App\Repositories\NotificationUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NotificationUserRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationUserRepository
     */
    protected $notificationUserRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->notificationUserRepo = \App::make(NotificationUserRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->make()->toArray();

        $createdNotificationUser = $this->notificationUserRepo->create($notificationUser);

        $createdNotificationUser = $createdNotificationUser->toArray();
        $this->assertArrayHasKey('id', $createdNotificationUser);
        $this->assertNotNull($createdNotificationUser['id'], 'Created NotificationUser must have id specified');
        $this->assertNotNull(NotificationUser::find($createdNotificationUser['id']), 'NotificationUser with given id must be in DB');
        $this->assertModelData($notificationUser, $createdNotificationUser);
    }

    /**
     * @test read
     */
    public function test_read_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->create();

        $dbNotificationUser = $this->notificationUserRepo->find($notificationUser->id);

        $dbNotificationUser = $dbNotificationUser->toArray();
        $this->assertModelData($notificationUser->toArray(), $dbNotificationUser);
    }

    /**
     * @test update
     */
    public function test_update_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->create();
        $fakeNotificationUser = factory(NotificationUser::class)->make()->toArray();

        $updatedNotificationUser = $this->notificationUserRepo->update($fakeNotificationUser, $notificationUser->id);

        $this->assertModelData($fakeNotificationUser, $updatedNotificationUser->toArray());
        $dbNotificationUser = $this->notificationUserRepo->find($notificationUser->id);
        $this->assertModelData($fakeNotificationUser, $dbNotificationUser->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_notification_user()
    {
        $notificationUser = factory(NotificationUser::class)->create();

        $resp = $this->notificationUserRepo->delete($notificationUser->id);

        $this->assertTrue($resp);
        $this->assertNull(NotificationUser::find($notificationUser->id), 'NotificationUser should not exist in DB');
    }
}
