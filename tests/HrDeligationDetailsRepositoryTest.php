<?php namespace Tests\Repositories;

use App\Models\HrDeligationDetails;
use App\Repositories\HrDeligationDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrDeligationDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrDeligationDetailsRepository
     */
    protected $hrDeligationDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrDeligationDetailsRepo = \App::make(HrDeligationDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->make()->toArray();

        $createdHrDeligationDetails = $this->hrDeligationDetailsRepo->create($hrDeligationDetails);

        $createdHrDeligationDetails = $createdHrDeligationDetails->toArray();
        $this->assertArrayHasKey('id', $createdHrDeligationDetails);
        $this->assertNotNull($createdHrDeligationDetails['id'], 'Created HrDeligationDetails must have id specified');
        $this->assertNotNull(HrDeligationDetails::find($createdHrDeligationDetails['id']), 'HrDeligationDetails with given id must be in DB');
        $this->assertModelData($hrDeligationDetails, $createdHrDeligationDetails);
    }

    /**
     * @test read
     */
    public function test_read_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->create();

        $dbHrDeligationDetails = $this->hrDeligationDetailsRepo->find($hrDeligationDetails->id);

        $dbHrDeligationDetails = $dbHrDeligationDetails->toArray();
        $this->assertModelData($hrDeligationDetails->toArray(), $dbHrDeligationDetails);
    }

    /**
     * @test update
     */
    public function test_update_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->create();
        $fakeHrDeligationDetails = factory(HrDeligationDetails::class)->make()->toArray();

        $updatedHrDeligationDetails = $this->hrDeligationDetailsRepo->update($fakeHrDeligationDetails, $hrDeligationDetails->id);

        $this->assertModelData($fakeHrDeligationDetails, $updatedHrDeligationDetails->toArray());
        $dbHrDeligationDetails = $this->hrDeligationDetailsRepo->find($hrDeligationDetails->id);
        $this->assertModelData($fakeHrDeligationDetails, $dbHrDeligationDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_deligation_details()
    {
        $hrDeligationDetails = factory(HrDeligationDetails::class)->create();

        $resp = $this->hrDeligationDetailsRepo->delete($hrDeligationDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(HrDeligationDetails::find($hrDeligationDetails->id), 'HrDeligationDetails should not exist in DB');
    }
}
