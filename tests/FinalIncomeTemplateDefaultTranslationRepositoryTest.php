<?php namespace Tests\Repositories;

use App\Models\FinalIncomeTemplateDefaultTranslation;
use App\Repositories\FinalIncomeTemplateDefaultTranslationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalIncomeTemplateDefaultTranslationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalIncomeTemplateDefaultTranslationRepository
     */
    protected $finalIncomeTemplateDefaultTranslationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalIncomeTemplateDefaultTranslationRepo = \App::make(FinalIncomeTemplateDefaultTranslationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->make()->toArray();

        $createdFinalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepo->create($finalIncomeTemplateDefaultTranslation);

        $createdFinalIncomeTemplateDefaultTranslation = $createdFinalIncomeTemplateDefaultTranslation->toArray();
        $this->assertArrayHasKey('id', $createdFinalIncomeTemplateDefaultTranslation);
        $this->assertNotNull($createdFinalIncomeTemplateDefaultTranslation['id'], 'Created FinalIncomeTemplateDefaultTranslation must have id specified');
        $this->assertNotNull(FinalIncomeTemplateDefaultTranslation::find($createdFinalIncomeTemplateDefaultTranslation['id']), 'FinalIncomeTemplateDefaultTranslation with given id must be in DB');
        $this->assertModelData($finalIncomeTemplateDefaultTranslation, $createdFinalIncomeTemplateDefaultTranslation);
    }

    /**
     * @test read
     */
    public function test_read_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->create();

        $dbFinalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepo->find($finalIncomeTemplateDefaultTranslation->id);

        $dbFinalIncomeTemplateDefaultTranslation = $dbFinalIncomeTemplateDefaultTranslation->toArray();
        $this->assertModelData($finalIncomeTemplateDefaultTranslation->toArray(), $dbFinalIncomeTemplateDefaultTranslation);
    }

    /**
     * @test update
     */
    public function test_update_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->create();
        $fakeFinalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->make()->toArray();

        $updatedFinalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepo->update($fakeFinalIncomeTemplateDefaultTranslation, $finalIncomeTemplateDefaultTranslation->id);

        $this->assertModelData($fakeFinalIncomeTemplateDefaultTranslation, $updatedFinalIncomeTemplateDefaultTranslation->toArray());
        $dbFinalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepo->find($finalIncomeTemplateDefaultTranslation->id);
        $this->assertModelData($fakeFinalIncomeTemplateDefaultTranslation, $dbFinalIncomeTemplateDefaultTranslation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_income_template_default_translation()
    {
        $finalIncomeTemplateDefaultTranslation = factory(FinalIncomeTemplateDefaultTranslation::class)->create();

        $resp = $this->finalIncomeTemplateDefaultTranslationRepo->delete($finalIncomeTemplateDefaultTranslation->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalIncomeTemplateDefaultTranslation::find($finalIncomeTemplateDefaultTranslation->id), 'FinalIncomeTemplateDefaultTranslation should not exist in DB');
    }
}
