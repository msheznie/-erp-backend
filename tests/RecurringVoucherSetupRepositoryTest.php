<?php namespace Tests\Repositories;

use App\Models\RecurringVoucherSetup;
use App\Repositories\RecurringVoucherSetupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RecurringVoucherSetupRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RecurringVoucherSetupRepository
     */
    protected $recurringVoucherSetupRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->recurringVoucherSetupRepo = \App::make(RecurringVoucherSetupRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->make()->toArray();

        $createdRecurringVoucherSetup = $this->recurringVoucherSetupRepo->create($recurringVoucherSetup);

        $createdRecurringVoucherSetup = $createdRecurringVoucherSetup->toArray();
        $this->assertArrayHasKey('id', $createdRecurringVoucherSetup);
        $this->assertNotNull($createdRecurringVoucherSetup['id'], 'Created RecurringVoucherSetup must have id specified');
        $this->assertNotNull(RecurringVoucherSetup::find($createdRecurringVoucherSetup['id']), 'RecurringVoucherSetup with given id must be in DB');
        $this->assertModelData($recurringVoucherSetup, $createdRecurringVoucherSetup);
    }

    /**
     * @test read
     */
    public function test_read_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->create();

        $dbRecurringVoucherSetup = $this->recurringVoucherSetupRepo->find($recurringVoucherSetup->id);

        $dbRecurringVoucherSetup = $dbRecurringVoucherSetup->toArray();
        $this->assertModelData($recurringVoucherSetup->toArray(), $dbRecurringVoucherSetup);
    }

    /**
     * @test update
     */
    public function test_update_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->create();
        $fakeRecurringVoucherSetup = factory(RecurringVoucherSetup::class)->make()->toArray();

        $updatedRecurringVoucherSetup = $this->recurringVoucherSetupRepo->update($fakeRecurringVoucherSetup, $recurringVoucherSetup->id);

        $this->assertModelData($fakeRecurringVoucherSetup, $updatedRecurringVoucherSetup->toArray());
        $dbRecurringVoucherSetup = $this->recurringVoucherSetupRepo->find($recurringVoucherSetup->id);
        $this->assertModelData($fakeRecurringVoucherSetup, $dbRecurringVoucherSetup->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->create();

        $resp = $this->recurringVoucherSetupRepo->delete($recurringVoucherSetup->id);

        $this->assertTrue($resp);
        $this->assertNull(RecurringVoucherSetup::find($recurringVoucherSetup->id), 'RecurringVoucherSetup should not exist in DB');
    }
}
