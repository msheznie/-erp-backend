<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeTemplateDetails;
use App\Repositories\FinalReturnIncomeTemplateDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeTemplateDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeTemplateDetailsRepository
     */
    protected $finalReturnIncomeTemplateDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeTemplateDetailsRepo = \App::make(FinalReturnIncomeTemplateDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->make()->toArray();

        $createdFinalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepo->create($finalReturnIncomeTemplateDetails);

        $createdFinalReturnIncomeTemplateDetails = $createdFinalReturnIncomeTemplateDetails->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeTemplateDetails);
        $this->assertNotNull($createdFinalReturnIncomeTemplateDetails['id'], 'Created FinalReturnIncomeTemplateDetails must have id specified');
        $this->assertNotNull(FinalReturnIncomeTemplateDetails::find($createdFinalReturnIncomeTemplateDetails['id']), 'FinalReturnIncomeTemplateDetails with given id must be in DB');
        $this->assertModelData($finalReturnIncomeTemplateDetails, $createdFinalReturnIncomeTemplateDetails);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->create();

        $dbFinalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepo->find($finalReturnIncomeTemplateDetails->id);

        $dbFinalReturnIncomeTemplateDetails = $dbFinalReturnIncomeTemplateDetails->toArray();
        $this->assertModelData($finalReturnIncomeTemplateDetails->toArray(), $dbFinalReturnIncomeTemplateDetails);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->create();
        $fakeFinalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->make()->toArray();

        $updatedFinalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepo->update($fakeFinalReturnIncomeTemplateDetails, $finalReturnIncomeTemplateDetails->id);

        $this->assertModelData($fakeFinalReturnIncomeTemplateDetails, $updatedFinalReturnIncomeTemplateDetails->toArray());
        $dbFinalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepo->find($finalReturnIncomeTemplateDetails->id);
        $this->assertModelData($fakeFinalReturnIncomeTemplateDetails, $dbFinalReturnIncomeTemplateDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_template_details()
    {
        $finalReturnIncomeTemplateDetails = factory(FinalReturnIncomeTemplateDetails::class)->create();

        $resp = $this->finalReturnIncomeTemplateDetailsRepo->delete($finalReturnIncomeTemplateDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeTemplateDetails::find($finalReturnIncomeTemplateDetails->id), 'FinalReturnIncomeTemplateDetails should not exist in DB');
    }
}
