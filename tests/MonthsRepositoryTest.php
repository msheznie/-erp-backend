<?php

use App\Models\Months;
use App\Repositories\MonthsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthsRepositoryTest extends TestCase
{
    use MakeMonthsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MonthsRepository
     */
    protected $monthsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->monthsRepo = App::make(MonthsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMonths()
    {
        $months = $this->fakeMonthsData();
        $createdMonths = $this->monthsRepo->create($months);
        $createdMonths = $createdMonths->toArray();
        $this->assertArrayHasKey('id', $createdMonths);
        $this->assertNotNull($createdMonths['id'], 'Created Months must have id specified');
        $this->assertNotNull(Months::find($createdMonths['id']), 'Months with given id must be in DB');
        $this->assertModelData($months, $createdMonths);
    }

    /**
     * @test read
     */
    public function testReadMonths()
    {
        $months = $this->makeMonths();
        $dbMonths = $this->monthsRepo->find($months->id);
        $dbMonths = $dbMonths->toArray();
        $this->assertModelData($months->toArray(), $dbMonths);
    }

    /**
     * @test update
     */
    public function testUpdateMonths()
    {
        $months = $this->makeMonths();
        $fakeMonths = $this->fakeMonthsData();
        $updatedMonths = $this->monthsRepo->update($fakeMonths, $months->id);
        $this->assertModelData($fakeMonths, $updatedMonths->toArray());
        $dbMonths = $this->monthsRepo->find($months->id);
        $this->assertModelData($fakeMonths, $dbMonths->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMonths()
    {
        $months = $this->makeMonths();
        $resp = $this->monthsRepo->delete($months->id);
        $this->assertTrue($resp);
        $this->assertNull(Months::find($months->id), 'Months should not exist in DB');
    }
}
