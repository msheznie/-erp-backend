<?php

use App\Models\UnitConversion;
use App\Repositories\UnitConversionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitConversionRepositoryTest extends TestCase
{
    use MakeUnitConversionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UnitConversionRepository
     */
    protected $unitConversionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->unitConversionRepo = App::make(UnitConversionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUnitConversion()
    {
        $unitConversion = $this->fakeUnitConversionData();
        $createdUnitConversion = $this->unitConversionRepo->create($unitConversion);
        $createdUnitConversion = $createdUnitConversion->toArray();
        $this->assertArrayHasKey('id', $createdUnitConversion);
        $this->assertNotNull($createdUnitConversion['id'], 'Created UnitConversion must have id specified');
        $this->assertNotNull(UnitConversion::find($createdUnitConversion['id']), 'UnitConversion with given id must be in DB');
        $this->assertModelData($unitConversion, $createdUnitConversion);
    }

    /**
     * @test read
     */
    public function testReadUnitConversion()
    {
        $unitConversion = $this->makeUnitConversion();
        $dbUnitConversion = $this->unitConversionRepo->find($unitConversion->id);
        $dbUnitConversion = $dbUnitConversion->toArray();
        $this->assertModelData($unitConversion->toArray(), $dbUnitConversion);
    }

    /**
     * @test update
     */
    public function testUpdateUnitConversion()
    {
        $unitConversion = $this->makeUnitConversion();
        $fakeUnitConversion = $this->fakeUnitConversionData();
        $updatedUnitConversion = $this->unitConversionRepo->update($fakeUnitConversion, $unitConversion->id);
        $this->assertModelData($fakeUnitConversion, $updatedUnitConversion->toArray());
        $dbUnitConversion = $this->unitConversionRepo->find($unitConversion->id);
        $this->assertModelData($fakeUnitConversion, $dbUnitConversion->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUnitConversion()
    {
        $unitConversion = $this->makeUnitConversion();
        $resp = $this->unitConversionRepo->delete($unitConversion->id);
        $this->assertTrue($resp);
        $this->assertNull(UnitConversion::find($unitConversion->id), 'UnitConversion should not exist in DB');
    }
}
