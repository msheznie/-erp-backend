<?php namespace Tests\Repositories;

use App\Models\POSSTAGShiftDetails;
use App\Repositories\POSSTAGShiftDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGShiftDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGShiftDetailsRepository
     */
    protected $pOSSTAGShiftDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGShiftDetailsRepo = \App::make(POSSTAGShiftDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->make()->toArray();

        $createdPOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepo->create($pOSSTAGShiftDetails);

        $createdPOSSTAGShiftDetails = $createdPOSSTAGShiftDetails->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGShiftDetails);
        $this->assertNotNull($createdPOSSTAGShiftDetails['id'], 'Created POSSTAGShiftDetails must have id specified');
        $this->assertNotNull(POSSTAGShiftDetails::find($createdPOSSTAGShiftDetails['id']), 'POSSTAGShiftDetails with given id must be in DB');
        $this->assertModelData($pOSSTAGShiftDetails, $createdPOSSTAGShiftDetails);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->create();

        $dbPOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepo->find($pOSSTAGShiftDetails->id);

        $dbPOSSTAGShiftDetails = $dbPOSSTAGShiftDetails->toArray();
        $this->assertModelData($pOSSTAGShiftDetails->toArray(), $dbPOSSTAGShiftDetails);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->create();
        $fakePOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->make()->toArray();

        $updatedPOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepo->update($fakePOSSTAGShiftDetails, $pOSSTAGShiftDetails->id);

        $this->assertModelData($fakePOSSTAGShiftDetails, $updatedPOSSTAGShiftDetails->toArray());
        $dbPOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepo->find($pOSSTAGShiftDetails->id);
        $this->assertModelData($fakePOSSTAGShiftDetails, $dbPOSSTAGShiftDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->create();

        $resp = $this->pOSSTAGShiftDetailsRepo->delete($pOSSTAGShiftDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGShiftDetails::find($pOSSTAGShiftDetails->id), 'POSSTAGShiftDetails should not exist in DB');
    }
}
