<?php namespace Tests\Repositories;

use App\Models\POSTransStatus;
use App\Repositories\POSTransStatusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSTransStatusRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSTransStatusRepository
     */
    protected $pOSTransStatusRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSTransStatusRepo = \App::make(POSTransStatusRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->make()->toArray();

        $createdPOSTransStatus = $this->pOSTransStatusRepo->create($pOSTransStatus);

        $createdPOSTransStatus = $createdPOSTransStatus->toArray();
        $this->assertArrayHasKey('id', $createdPOSTransStatus);
        $this->assertNotNull($createdPOSTransStatus['id'], 'Created POSTransStatus must have id specified');
        $this->assertNotNull(POSTransStatus::find($createdPOSTransStatus['id']), 'POSTransStatus with given id must be in DB');
        $this->assertModelData($pOSTransStatus, $createdPOSTransStatus);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->create();

        $dbPOSTransStatus = $this->pOSTransStatusRepo->find($pOSTransStatus->id);

        $dbPOSTransStatus = $dbPOSTransStatus->toArray();
        $this->assertModelData($pOSTransStatus->toArray(), $dbPOSTransStatus);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->create();
        $fakePOSTransStatus = factory(POSTransStatus::class)->make()->toArray();

        $updatedPOSTransStatus = $this->pOSTransStatusRepo->update($fakePOSTransStatus, $pOSTransStatus->id);

        $this->assertModelData($fakePOSTransStatus, $updatedPOSTransStatus->toArray());
        $dbPOSTransStatus = $this->pOSTransStatusRepo->find($pOSTransStatus->id);
        $this->assertModelData($fakePOSTransStatus, $dbPOSTransStatus->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_trans_status()
    {
        $pOSTransStatus = factory(POSTransStatus::class)->create();

        $resp = $this->pOSTransStatusRepo->delete($pOSTransStatus->id);

        $this->assertTrue($resp);
        $this->assertNull(POSTransStatus::find($pOSTransStatus->id), 'POSTransStatus should not exist in DB');
    }
}
