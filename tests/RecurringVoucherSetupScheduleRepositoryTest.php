<?php namespace Tests\Repositories;

use App\Models\RecurringVoucherSetupSchedule;
use App\Repositories\RecurringVoucherSetupScheduleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RecurringVoucherSetupScheduleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RecurringVoucherSetupScheduleRepository
     */
    protected $recurringVoucherSetupScheduleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->recurringVoucherSetupScheduleRepo = \App::make(RecurringVoucherSetupScheduleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->make()->toArray();

        $createdRecurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepo->create($recurringVoucherSetupSchedule);

        $createdRecurringVoucherSetupSchedule = $createdRecurringVoucherSetupSchedule->toArray();
        $this->assertArrayHasKey('id', $createdRecurringVoucherSetupSchedule);
        $this->assertNotNull($createdRecurringVoucherSetupSchedule['id'], 'Created RecurringVoucherSetupSchedule must have id specified');
        $this->assertNotNull(RecurringVoucherSetupSchedule::find($createdRecurringVoucherSetupSchedule['id']), 'RecurringVoucherSetupSchedule with given id must be in DB');
        $this->assertModelData($recurringVoucherSetupSchedule, $createdRecurringVoucherSetupSchedule);
    }

    /**
     * @test read
     */
    public function test_read_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->create();

        $dbRecurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepo->find($recurringVoucherSetupSchedule->id);

        $dbRecurringVoucherSetupSchedule = $dbRecurringVoucherSetupSchedule->toArray();
        $this->assertModelData($recurringVoucherSetupSchedule->toArray(), $dbRecurringVoucherSetupSchedule);
    }

    /**
     * @test update
     */
    public function test_update_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->create();
        $fakeRecurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->make()->toArray();

        $updatedRecurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepo->update($fakeRecurringVoucherSetupSchedule, $recurringVoucherSetupSchedule->id);

        $this->assertModelData($fakeRecurringVoucherSetupSchedule, $updatedRecurringVoucherSetupSchedule->toArray());
        $dbRecurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepo->find($recurringVoucherSetupSchedule->id);
        $this->assertModelData($fakeRecurringVoucherSetupSchedule, $dbRecurringVoucherSetupSchedule->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->create();

        $resp = $this->recurringVoucherSetupScheduleRepo->delete($recurringVoucherSetupSchedule->id);

        $this->assertTrue($resp);
        $this->assertNull(RecurringVoucherSetupSchedule::find($recurringVoucherSetupSchedule->id), 'RecurringVoucherSetupSchedule should not exist in DB');
    }
}
