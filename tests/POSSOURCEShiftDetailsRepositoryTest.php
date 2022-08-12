<?php namespace Tests\Repositories;

use App\Models\POSSOURCEShiftDetails;
use App\Repositories\POSSOURCEShiftDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCEShiftDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCEShiftDetailsRepository
     */
    protected $pOSSOURCEShiftDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCEShiftDetailsRepo = \App::make(POSSOURCEShiftDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->make()->toArray();

        $createdPOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepo->create($pOSSOURCEShiftDetails);

        $createdPOSSOURCEShiftDetails = $createdPOSSOURCEShiftDetails->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCEShiftDetails);
        $this->assertNotNull($createdPOSSOURCEShiftDetails['id'], 'Created POSSOURCEShiftDetails must have id specified');
        $this->assertNotNull(POSSOURCEShiftDetails::find($createdPOSSOURCEShiftDetails['id']), 'POSSOURCEShiftDetails with given id must be in DB');
        $this->assertModelData($pOSSOURCEShiftDetails, $createdPOSSOURCEShiftDetails);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->create();

        $dbPOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepo->find($pOSSOURCEShiftDetails->id);

        $dbPOSSOURCEShiftDetails = $dbPOSSOURCEShiftDetails->toArray();
        $this->assertModelData($pOSSOURCEShiftDetails->toArray(), $dbPOSSOURCEShiftDetails);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->create();
        $fakePOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->make()->toArray();

        $updatedPOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepo->update($fakePOSSOURCEShiftDetails, $pOSSOURCEShiftDetails->id);

        $this->assertModelData($fakePOSSOURCEShiftDetails, $updatedPOSSOURCEShiftDetails->toArray());
        $dbPOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepo->find($pOSSOURCEShiftDetails->id);
        $this->assertModelData($fakePOSSOURCEShiftDetails, $dbPOSSOURCEShiftDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->create();

        $resp = $this->pOSSOURCEShiftDetailsRepo->delete($pOSSOURCEShiftDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCEShiftDetails::find($pOSSOURCEShiftDetails->id), 'POSSOURCEShiftDetails should not exist in DB');
    }
}
