<?php

use App\Models\ErpLocation;
use App\Repositories\ErpLocationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErpLocationRepositoryTest extends TestCase
{
    use MakeErpLocationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpLocationRepository
     */
    protected $erpLocationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->erpLocationRepo = App::make(ErpLocationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateErpLocation()
    {
        $erpLocation = $this->fakeErpLocationData();
        $createdErpLocation = $this->erpLocationRepo->create($erpLocation);
        $createdErpLocation = $createdErpLocation->toArray();
        $this->assertArrayHasKey('id', $createdErpLocation);
        $this->assertNotNull($createdErpLocation['id'], 'Created ErpLocation must have id specified');
        $this->assertNotNull(ErpLocation::find($createdErpLocation['id']), 'ErpLocation with given id must be in DB');
        $this->assertModelData($erpLocation, $createdErpLocation);
    }

    /**
     * @test read
     */
    public function testReadErpLocation()
    {
        $erpLocation = $this->makeErpLocation();
        $dbErpLocation = $this->erpLocationRepo->find($erpLocation->id);
        $dbErpLocation = $dbErpLocation->toArray();
        $this->assertModelData($erpLocation->toArray(), $dbErpLocation);
    }

    /**
     * @test update
     */
    public function testUpdateErpLocation()
    {
        $erpLocation = $this->makeErpLocation();
        $fakeErpLocation = $this->fakeErpLocationData();
        $updatedErpLocation = $this->erpLocationRepo->update($fakeErpLocation, $erpLocation->id);
        $this->assertModelData($fakeErpLocation, $updatedErpLocation->toArray());
        $dbErpLocation = $this->erpLocationRepo->find($erpLocation->id);
        $this->assertModelData($fakeErpLocation, $dbErpLocation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteErpLocation()
    {
        $erpLocation = $this->makeErpLocation();
        $resp = $this->erpLocationRepo->delete($erpLocation->id);
        $this->assertTrue($resp);
        $this->assertNull(ErpLocation::find($erpLocation->id), 'ErpLocation should not exist in DB');
    }
}
