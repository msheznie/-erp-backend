<?php

use App\Models\MonthlyAdditionsMaster;
use App\Repositories\MonthlyAdditionsMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthlyAdditionsMasterRepositoryTest extends TestCase
{
    use MakeMonthlyAdditionsMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MonthlyAdditionsMasterRepository
     */
    protected $monthlyAdditionsMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->monthlyAdditionsMasterRepo = App::make(MonthlyAdditionsMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->fakeMonthlyAdditionsMasterData();
        $createdMonthlyAdditionsMaster = $this->monthlyAdditionsMasterRepo->create($monthlyAdditionsMaster);
        $createdMonthlyAdditionsMaster = $createdMonthlyAdditionsMaster->toArray();
        $this->assertArrayHasKey('id', $createdMonthlyAdditionsMaster);
        $this->assertNotNull($createdMonthlyAdditionsMaster['id'], 'Created MonthlyAdditionsMaster must have id specified');
        $this->assertNotNull(MonthlyAdditionsMaster::find($createdMonthlyAdditionsMaster['id']), 'MonthlyAdditionsMaster with given id must be in DB');
        $this->assertModelData($monthlyAdditionsMaster, $createdMonthlyAdditionsMaster);
    }

    /**
     * @test read
     */
    public function testReadMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->makeMonthlyAdditionsMaster();
        $dbMonthlyAdditionsMaster = $this->monthlyAdditionsMasterRepo->find($monthlyAdditionsMaster->id);
        $dbMonthlyAdditionsMaster = $dbMonthlyAdditionsMaster->toArray();
        $this->assertModelData($monthlyAdditionsMaster->toArray(), $dbMonthlyAdditionsMaster);
    }

    /**
     * @test update
     */
    public function testUpdateMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->makeMonthlyAdditionsMaster();
        $fakeMonthlyAdditionsMaster = $this->fakeMonthlyAdditionsMasterData();
        $updatedMonthlyAdditionsMaster = $this->monthlyAdditionsMasterRepo->update($fakeMonthlyAdditionsMaster, $monthlyAdditionsMaster->id);
        $this->assertModelData($fakeMonthlyAdditionsMaster, $updatedMonthlyAdditionsMaster->toArray());
        $dbMonthlyAdditionsMaster = $this->monthlyAdditionsMasterRepo->find($monthlyAdditionsMaster->id);
        $this->assertModelData($fakeMonthlyAdditionsMaster, $dbMonthlyAdditionsMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->makeMonthlyAdditionsMaster();
        $resp = $this->monthlyAdditionsMasterRepo->delete($monthlyAdditionsMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(MonthlyAdditionsMaster::find($monthlyAdditionsMaster->id), 'MonthlyAdditionsMaster should not exist in DB');
    }
}
