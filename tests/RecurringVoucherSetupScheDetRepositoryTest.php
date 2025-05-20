<?php namespace Tests\Repositories;

use App\Models\RecurringVoucherSetupScheDet;
use App\Repositories\RecurringVoucherSetupScheDetRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RecurringVoucherSetupScheDetRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RecurringVoucherSetupScheDetRepository
     */
    protected $recurringVoucherSetupScheDetRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->recurringVoucherSetupScheDetRepo = \App::make(RecurringVoucherSetupScheDetRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->make()->toArray();

        $createdRecurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepo->create($recurringVoucherSetupScheDet);

        $createdRecurringVoucherSetupScheDet = $createdRecurringVoucherSetupScheDet->toArray();
        $this->assertArrayHasKey('id', $createdRecurringVoucherSetupScheDet);
        $this->assertNotNull($createdRecurringVoucherSetupScheDet['id'], 'Created RecurringVoucherSetupScheDet must have id specified');
        $this->assertNotNull(RecurringVoucherSetupScheDet::find($createdRecurringVoucherSetupScheDet['id']), 'RecurringVoucherSetupScheDet with given id must be in DB');
        $this->assertModelData($recurringVoucherSetupScheDet, $createdRecurringVoucherSetupScheDet);
    }

    /**
     * @test read
     */
    public function test_read_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->create();

        $dbRecurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepo->find($recurringVoucherSetupScheDet->id);

        $dbRecurringVoucherSetupScheDet = $dbRecurringVoucherSetupScheDet->toArray();
        $this->assertModelData($recurringVoucherSetupScheDet->toArray(), $dbRecurringVoucherSetupScheDet);
    }

    /**
     * @test update
     */
    public function test_update_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->create();
        $fakeRecurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->make()->toArray();

        $updatedRecurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepo->update($fakeRecurringVoucherSetupScheDet, $recurringVoucherSetupScheDet->id);

        $this->assertModelData($fakeRecurringVoucherSetupScheDet, $updatedRecurringVoucherSetupScheDet->toArray());
        $dbRecurringVoucherSetupScheDet = $this->recurringVoucherSetupScheDetRepo->find($recurringVoucherSetupScheDet->id);
        $this->assertModelData($fakeRecurringVoucherSetupScheDet, $dbRecurringVoucherSetupScheDet->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->create();

        $resp = $this->recurringVoucherSetupScheDetRepo->delete($recurringVoucherSetupScheDet->id);

        $this->assertTrue($resp);
        $this->assertNull(RecurringVoucherSetupScheDet::find($recurringVoucherSetupScheDet->id), 'RecurringVoucherSetupScheDet should not exist in DB');
    }
}
