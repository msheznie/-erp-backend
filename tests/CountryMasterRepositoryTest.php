<?php

use App\Models\CountryMaster;
use App\Repositories\CountryMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryMasterRepositoryTest extends TestCase
{
    use MakeCountryMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CountryMasterRepository
     */
    protected $countryMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->countryMasterRepo = App::make(CountryMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCountryMaster()
    {
        $countryMaster = $this->fakeCountryMasterData();
        $createdCountryMaster = $this->countryMasterRepo->create($countryMaster);
        $createdCountryMaster = $createdCountryMaster->toArray();
        $this->assertArrayHasKey('id', $createdCountryMaster);
        $this->assertNotNull($createdCountryMaster['id'], 'Created CountryMaster must have id specified');
        $this->assertNotNull(CountryMaster::find($createdCountryMaster['id']), 'CountryMaster with given id must be in DB');
        $this->assertModelData($countryMaster, $createdCountryMaster);
    }

    /**
     * @test read
     */
    public function testReadCountryMaster()
    {
        $countryMaster = $this->makeCountryMaster();
        $dbCountryMaster = $this->countryMasterRepo->find($countryMaster->id);
        $dbCountryMaster = $dbCountryMaster->toArray();
        $this->assertModelData($countryMaster->toArray(), $dbCountryMaster);
    }

    /**
     * @test update
     */
    public function testUpdateCountryMaster()
    {
        $countryMaster = $this->makeCountryMaster();
        $fakeCountryMaster = $this->fakeCountryMasterData();
        $updatedCountryMaster = $this->countryMasterRepo->update($fakeCountryMaster, $countryMaster->id);
        $this->assertModelData($fakeCountryMaster, $updatedCountryMaster->toArray());
        $dbCountryMaster = $this->countryMasterRepo->find($countryMaster->id);
        $this->assertModelData($fakeCountryMaster, $dbCountryMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCountryMaster()
    {
        $countryMaster = $this->makeCountryMaster();
        $resp = $this->countryMasterRepo->delete($countryMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CountryMaster::find($countryMaster->id), 'CountryMaster should not exist in DB');
    }
}
