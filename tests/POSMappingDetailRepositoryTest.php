<?php namespace Tests\Repositories;

use App\Models\POSMappingDetail;
use App\Repositories\POSMappingDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSMappingDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSMappingDetailRepository
     */
    protected $pOSMappingDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSMappingDetailRepo = \App::make(POSMappingDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->make()->toArray();

        $createdPOSMappingDetail = $this->pOSMappingDetailRepo->create($pOSMappingDetail);

        $createdPOSMappingDetail = $createdPOSMappingDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSMappingDetail);
        $this->assertNotNull($createdPOSMappingDetail['id'], 'Created POSMappingDetail must have id specified');
        $this->assertNotNull(POSMappingDetail::find($createdPOSMappingDetail['id']), 'POSMappingDetail with given id must be in DB');
        $this->assertModelData($pOSMappingDetail, $createdPOSMappingDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->create();

        $dbPOSMappingDetail = $this->pOSMappingDetailRepo->find($pOSMappingDetail->id);

        $dbPOSMappingDetail = $dbPOSMappingDetail->toArray();
        $this->assertModelData($pOSMappingDetail->toArray(), $dbPOSMappingDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->create();
        $fakePOSMappingDetail = factory(POSMappingDetail::class)->make()->toArray();

        $updatedPOSMappingDetail = $this->pOSMappingDetailRepo->update($fakePOSMappingDetail, $pOSMappingDetail->id);

        $this->assertModelData($fakePOSMappingDetail, $updatedPOSMappingDetail->toArray());
        $dbPOSMappingDetail = $this->pOSMappingDetailRepo->find($pOSMappingDetail->id);
        $this->assertModelData($fakePOSMappingDetail, $dbPOSMappingDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_mapping_detail()
    {
        $pOSMappingDetail = factory(POSMappingDetail::class)->create();

        $resp = $this->pOSMappingDetailRepo->delete($pOSMappingDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSMappingDetail::find($pOSMappingDetail->id), 'POSMappingDetail should not exist in DB');
    }
}
