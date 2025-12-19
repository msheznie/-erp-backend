<?php namespace Tests\Repositories;

use App\Models\ChequeUpdateReason;
use App\Repositories\ChequeUpdateReasonRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ChequeUpdateReasonRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChequeUpdateReasonRepository
     */
    protected $chequeUpdateReasonRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chequeUpdateReasonRepo = \App::make(ChequeUpdateReasonRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->make()->toArray();

        $createdChequeUpdateReason = $this->chequeUpdateReasonRepo->create($chequeUpdateReason);

        $createdChequeUpdateReason = $createdChequeUpdateReason->toArray();
        $this->assertArrayHasKey('id', $createdChequeUpdateReason);
        $this->assertNotNull($createdChequeUpdateReason['id'], 'Created ChequeUpdateReason must have id specified');
        $this->assertNotNull(ChequeUpdateReason::find($createdChequeUpdateReason['id']), 'ChequeUpdateReason with given id must be in DB');
        $this->assertModelData($chequeUpdateReason, $createdChequeUpdateReason);
    }

    /**
     * @test read
     */
    public function test_read_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->create();

        $dbChequeUpdateReason = $this->chequeUpdateReasonRepo->find($chequeUpdateReason->id);

        $dbChequeUpdateReason = $dbChequeUpdateReason->toArray();
        $this->assertModelData($chequeUpdateReason->toArray(), $dbChequeUpdateReason);
    }

    /**
     * @test update
     */
    public function test_update_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->create();
        $fakeChequeUpdateReason = factory(ChequeUpdateReason::class)->make()->toArray();

        $updatedChequeUpdateReason = $this->chequeUpdateReasonRepo->update($fakeChequeUpdateReason, $chequeUpdateReason->id);

        $this->assertModelData($fakeChequeUpdateReason, $updatedChequeUpdateReason->toArray());
        $dbChequeUpdateReason = $this->chequeUpdateReasonRepo->find($chequeUpdateReason->id);
        $this->assertModelData($fakeChequeUpdateReason, $dbChequeUpdateReason->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cheque_update_reason()
    {
        $chequeUpdateReason = factory(ChequeUpdateReason::class)->create();

        $resp = $this->chequeUpdateReasonRepo->delete($chequeUpdateReason->id);

        $this->assertTrue($resp);
        $this->assertNull(ChequeUpdateReason::find($chequeUpdateReason->id), 'ChequeUpdateReason should not exist in DB');
    }
}
