<?php namespace Tests\Repositories;

use App\Models\HREmpContractHistory;
use App\Repositories\HREmpContractHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HREmpContractHistoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HREmpContractHistoryRepository
     */
    protected $hREmpContractHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hREmpContractHistoryRepo = \App::make(HREmpContractHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->make()->toArray();

        $createdHREmpContractHistory = $this->hREmpContractHistoryRepo->create($hREmpContractHistory);

        $createdHREmpContractHistory = $createdHREmpContractHistory->toArray();
        $this->assertArrayHasKey('id', $createdHREmpContractHistory);
        $this->assertNotNull($createdHREmpContractHistory['id'], 'Created HREmpContractHistory must have id specified');
        $this->assertNotNull(HREmpContractHistory::find($createdHREmpContractHistory['id']), 'HREmpContractHistory with given id must be in DB');
        $this->assertModelData($hREmpContractHistory, $createdHREmpContractHistory);
    }

    /**
     * @test read
     */
    public function test_read_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->create();

        $dbHREmpContractHistory = $this->hREmpContractHistoryRepo->find($hREmpContractHistory->id);

        $dbHREmpContractHistory = $dbHREmpContractHistory->toArray();
        $this->assertModelData($hREmpContractHistory->toArray(), $dbHREmpContractHistory);
    }

    /**
     * @test update
     */
    public function test_update_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->create();
        $fakeHREmpContractHistory = factory(HREmpContractHistory::class)->make()->toArray();

        $updatedHREmpContractHistory = $this->hREmpContractHistoryRepo->update($fakeHREmpContractHistory, $hREmpContractHistory->id);

        $this->assertModelData($fakeHREmpContractHistory, $updatedHREmpContractHistory->toArray());
        $dbHREmpContractHistory = $this->hREmpContractHistoryRepo->find($hREmpContractHistory->id);
        $this->assertModelData($fakeHREmpContractHistory, $dbHREmpContractHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_emp_contract_history()
    {
        $hREmpContractHistory = factory(HREmpContractHistory::class)->create();

        $resp = $this->hREmpContractHistoryRepo->delete($hREmpContractHistory->id);

        $this->assertTrue($resp);
        $this->assertNull(HREmpContractHistory::find($hREmpContractHistory->id), 'HREmpContractHistory should not exist in DB');
    }
}
