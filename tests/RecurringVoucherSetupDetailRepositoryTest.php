<?php namespace Tests\Repositories;

use App\Models\RecurringVoucherSetupDetail;
use App\Repositories\RecurringVoucherSetupDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RecurringVoucherSetupDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RecurringVoucherSetupDetailRepository
     */
    protected $recurringVoucherSetupDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->recurringVoucherSetupDetailRepo = \App::make(RecurringVoucherSetupDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->make()->toArray();

        $createdRecurringVoucherSetupDetail = $this->recurringVoucherSetupDetailRepo->create($recurringVoucherSetupDetail);

        $createdRecurringVoucherSetupDetail = $createdRecurringVoucherSetupDetail->toArray();
        $this->assertArrayHasKey('id', $createdRecurringVoucherSetupDetail);
        $this->assertNotNull($createdRecurringVoucherSetupDetail['id'], 'Created RecurringVoucherSetupDetail must have id specified');
        $this->assertNotNull(RecurringVoucherSetupDetail::find($createdRecurringVoucherSetupDetail['id']), 'RecurringVoucherSetupDetail with given id must be in DB');
        $this->assertModelData($recurringVoucherSetupDetail, $createdRecurringVoucherSetupDetail);
    }

    /**
     * @test read
     */
    public function test_read_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->create();

        $dbRecurringVoucherSetupDetail = $this->recurringVoucherSetupDetailRepo->find($recurringVoucherSetupDetail->id);

        $dbRecurringVoucherSetupDetail = $dbRecurringVoucherSetupDetail->toArray();
        $this->assertModelData($recurringVoucherSetupDetail->toArray(), $dbRecurringVoucherSetupDetail);
    }

    /**
     * @test update
     */
    public function test_update_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->create();
        $fakeRecurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->make()->toArray();

        $updatedRecurringVoucherSetupDetail = $this->recurringVoucherSetupDetailRepo->update($fakeRecurringVoucherSetupDetail, $recurringVoucherSetupDetail->id);

        $this->assertModelData($fakeRecurringVoucherSetupDetail, $updatedRecurringVoucherSetupDetail->toArray());
        $dbRecurringVoucherSetupDetail = $this->recurringVoucherSetupDetailRepo->find($recurringVoucherSetupDetail->id);
        $this->assertModelData($fakeRecurringVoucherSetupDetail, $dbRecurringVoucherSetupDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->create();

        $resp = $this->recurringVoucherSetupDetailRepo->delete($recurringVoucherSetupDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(RecurringVoucherSetupDetail::find($recurringVoucherSetupDetail->id), 'RecurringVoucherSetupDetail should not exist in DB');
    }
}
