<?php namespace Tests\Repositories;

use App\Models\MonthlyDeclarationsTypes;
use App\Repositories\MonthlyDeclarationsTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MonthlyDeclarationsTypesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MonthlyDeclarationsTypesRepository
     */
    protected $monthlyDeclarationsTypesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->monthlyDeclarationsTypesRepo = \App::make(MonthlyDeclarationsTypesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->make()->toArray();

        $createdMonthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepo->create($monthlyDeclarationsTypes);

        $createdMonthlyDeclarationsTypes = $createdMonthlyDeclarationsTypes->toArray();
        $this->assertArrayHasKey('id', $createdMonthlyDeclarationsTypes);
        $this->assertNotNull($createdMonthlyDeclarationsTypes['id'], 'Created MonthlyDeclarationsTypes must have id specified');
        $this->assertNotNull(MonthlyDeclarationsTypes::find($createdMonthlyDeclarationsTypes['id']), 'MonthlyDeclarationsTypes with given id must be in DB');
        $this->assertModelData($monthlyDeclarationsTypes, $createdMonthlyDeclarationsTypes);
    }

    /**
     * @test read
     */
    public function test_read_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->create();

        $dbMonthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepo->find($monthlyDeclarationsTypes->id);

        $dbMonthlyDeclarationsTypes = $dbMonthlyDeclarationsTypes->toArray();
        $this->assertModelData($monthlyDeclarationsTypes->toArray(), $dbMonthlyDeclarationsTypes);
    }

    /**
     * @test update
     */
    public function test_update_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->create();
        $fakeMonthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->make()->toArray();

        $updatedMonthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepo->update($fakeMonthlyDeclarationsTypes, $monthlyDeclarationsTypes->id);

        $this->assertModelData($fakeMonthlyDeclarationsTypes, $updatedMonthlyDeclarationsTypes->toArray());
        $dbMonthlyDeclarationsTypes = $this->monthlyDeclarationsTypesRepo->find($monthlyDeclarationsTypes->id);
        $this->assertModelData($fakeMonthlyDeclarationsTypes, $dbMonthlyDeclarationsTypes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->create();

        $resp = $this->monthlyDeclarationsTypesRepo->delete($monthlyDeclarationsTypes->id);

        $this->assertTrue($resp);
        $this->assertNull(MonthlyDeclarationsTypes::find($monthlyDeclarationsTypes->id), 'MonthlyDeclarationsTypes should not exist in DB');
    }
}
