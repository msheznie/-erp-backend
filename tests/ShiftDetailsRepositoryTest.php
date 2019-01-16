<?php

use App\Models\ShiftDetails;
use App\Repositories\ShiftDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShiftDetailsRepositoryTest extends TestCase
{
    use MakeShiftDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ShiftDetailsRepository
     */
    protected $shiftDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->shiftDetailsRepo = App::make(ShiftDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateShiftDetails()
    {
        $shiftDetails = $this->fakeShiftDetailsData();
        $createdShiftDetails = $this->shiftDetailsRepo->create($shiftDetails);
        $createdShiftDetails = $createdShiftDetails->toArray();
        $this->assertArrayHasKey('id', $createdShiftDetails);
        $this->assertNotNull($createdShiftDetails['id'], 'Created ShiftDetails must have id specified');
        $this->assertNotNull(ShiftDetails::find($createdShiftDetails['id']), 'ShiftDetails with given id must be in DB');
        $this->assertModelData($shiftDetails, $createdShiftDetails);
    }

    /**
     * @test read
     */
    public function testReadShiftDetails()
    {
        $shiftDetails = $this->makeShiftDetails();
        $dbShiftDetails = $this->shiftDetailsRepo->find($shiftDetails->id);
        $dbShiftDetails = $dbShiftDetails->toArray();
        $this->assertModelData($shiftDetails->toArray(), $dbShiftDetails);
    }

    /**
     * @test update
     */
    public function testUpdateShiftDetails()
    {
        $shiftDetails = $this->makeShiftDetails();
        $fakeShiftDetails = $this->fakeShiftDetailsData();
        $updatedShiftDetails = $this->shiftDetailsRepo->update($fakeShiftDetails, $shiftDetails->id);
        $this->assertModelData($fakeShiftDetails, $updatedShiftDetails->toArray());
        $dbShiftDetails = $this->shiftDetailsRepo->find($shiftDetails->id);
        $this->assertModelData($fakeShiftDetails, $dbShiftDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteShiftDetails()
    {
        $shiftDetails = $this->makeShiftDetails();
        $resp = $this->shiftDetailsRepo->delete($shiftDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(ShiftDetails::find($shiftDetails->id), 'ShiftDetails should not exist in DB');
    }
}
