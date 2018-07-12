<?php

use App\Models\Year;
use App\Repositories\YearRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YearRepositoryTest extends TestCase
{
    use MakeYearTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var YearRepository
     */
    protected $yearRepo;

    public function setUp()
    {
        parent::setUp();
        $this->yearRepo = App::make(YearRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateYear()
    {
        $year = $this->fakeYearData();
        $createdYear = $this->yearRepo->create($year);
        $createdYear = $createdYear->toArray();
        $this->assertArrayHasKey('id', $createdYear);
        $this->assertNotNull($createdYear['id'], 'Created Year must have id specified');
        $this->assertNotNull(Year::find($createdYear['id']), 'Year with given id must be in DB');
        $this->assertModelData($year, $createdYear);
    }

    /**
     * @test read
     */
    public function testReadYear()
    {
        $year = $this->makeYear();
        $dbYear = $this->yearRepo->find($year->id);
        $dbYear = $dbYear->toArray();
        $this->assertModelData($year->toArray(), $dbYear);
    }

    /**
     * @test update
     */
    public function testUpdateYear()
    {
        $year = $this->makeYear();
        $fakeYear = $this->fakeYearData();
        $updatedYear = $this->yearRepo->update($fakeYear, $year->id);
        $this->assertModelData($fakeYear, $updatedYear->toArray());
        $dbYear = $this->yearRepo->find($year->id);
        $this->assertModelData($fakeYear, $dbYear->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteYear()
    {
        $year = $this->makeYear();
        $resp = $this->yearRepo->delete($year->id);
        $this->assertTrue($resp);
        $this->assertNull(Year::find($year->id), 'Year should not exist in DB');
    }
}
