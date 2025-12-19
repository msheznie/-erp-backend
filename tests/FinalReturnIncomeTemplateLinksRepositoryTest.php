<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeTemplateLinks;
use App\Repositories\FinalReturnIncomeTemplateLinksRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeTemplateLinksRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeTemplateLinksRepository
     */
    protected $finalReturnIncomeTemplateLinksRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeTemplateLinksRepo = \App::make(FinalReturnIncomeTemplateLinksRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_template_links()
    {
        $finalReturnIncomeTemplateLinks = factory(FinalReturnIncomeTemplateLinks::class)->make()->toArray();

        $createdFinalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepo->create($finalReturnIncomeTemplateLinks);

        $createdFinalReturnIncomeTemplateLinks = $createdFinalReturnIncomeTemplateLinks->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeTemplateLinks);
        $this->assertNotNull($createdFinalReturnIncomeTemplateLinks['id'], 'Created FinalReturnIncomeTemplateLinks must have id specified');
        $this->assertNotNull(FinalReturnIncomeTemplateLinks::find($createdFinalReturnIncomeTemplateLinks['id']), 'FinalReturnIncomeTemplateLinks with given id must be in DB');
        $this->assertModelData($finalReturnIncomeTemplateLinks, $createdFinalReturnIncomeTemplateLinks);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_template_links()
    {
        $finalReturnIncomeTemplateLinks = factory(FinalReturnIncomeTemplateLinks::class)->create();

        $dbFinalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepo->find($finalReturnIncomeTemplateLinks->id);

        $dbFinalReturnIncomeTemplateLinks = $dbFinalReturnIncomeTemplateLinks->toArray();
        $this->assertModelData($finalReturnIncomeTemplateLinks->toArray(), $dbFinalReturnIncomeTemplateLinks);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_template_links()
    {
        $finalReturnIncomeTemplateLinks = factory(FinalReturnIncomeTemplateLinks::class)->create();
        $fakeFinalReturnIncomeTemplateLinks = factory(FinalReturnIncomeTemplateLinks::class)->make()->toArray();

        $updatedFinalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepo->update($fakeFinalReturnIncomeTemplateLinks, $finalReturnIncomeTemplateLinks->id);

        $this->assertModelData($fakeFinalReturnIncomeTemplateLinks, $updatedFinalReturnIncomeTemplateLinks->toArray());
        $dbFinalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepo->find($finalReturnIncomeTemplateLinks->id);
        $this->assertModelData($fakeFinalReturnIncomeTemplateLinks, $dbFinalReturnIncomeTemplateLinks->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_template_links()
    {
        $finalReturnIncomeTemplateLinks = factory(FinalReturnIncomeTemplateLinks::class)->create();

        $resp = $this->finalReturnIncomeTemplateLinksRepo->delete($finalReturnIncomeTemplateLinks->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeTemplateLinks::find($finalReturnIncomeTemplateLinks->id), 'FinalReturnIncomeTemplateLinks should not exist in DB');
    }
}
