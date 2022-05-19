<?php namespace Tests\Repositories;

use App\Models\TenderCriteriaAnswerType;
use App\Repositories\TenderCriteriaAnswerTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderCriteriaAnswerTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderCriteriaAnswerTypeRepository
     */
    protected $tenderCriteriaAnswerTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderCriteriaAnswerTypeRepo = \App::make(TenderCriteriaAnswerTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->make()->toArray();

        $createdTenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepo->create($tenderCriteriaAnswerType);

        $createdTenderCriteriaAnswerType = $createdTenderCriteriaAnswerType->toArray();
        $this->assertArrayHasKey('id', $createdTenderCriteriaAnswerType);
        $this->assertNotNull($createdTenderCriteriaAnswerType['id'], 'Created TenderCriteriaAnswerType must have id specified');
        $this->assertNotNull(TenderCriteriaAnswerType::find($createdTenderCriteriaAnswerType['id']), 'TenderCriteriaAnswerType with given id must be in DB');
        $this->assertModelData($tenderCriteriaAnswerType, $createdTenderCriteriaAnswerType);
    }

    /**
     * @test read
     */
    public function test_read_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->create();

        $dbTenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepo->find($tenderCriteriaAnswerType->id);

        $dbTenderCriteriaAnswerType = $dbTenderCriteriaAnswerType->toArray();
        $this->assertModelData($tenderCriteriaAnswerType->toArray(), $dbTenderCriteriaAnswerType);
    }

    /**
     * @test update
     */
    public function test_update_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->create();
        $fakeTenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->make()->toArray();

        $updatedTenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepo->update($fakeTenderCriteriaAnswerType, $tenderCriteriaAnswerType->id);

        $this->assertModelData($fakeTenderCriteriaAnswerType, $updatedTenderCriteriaAnswerType->toArray());
        $dbTenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepo->find($tenderCriteriaAnswerType->id);
        $this->assertModelData($fakeTenderCriteriaAnswerType, $dbTenderCriteriaAnswerType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_criteria_answer_type()
    {
        $tenderCriteriaAnswerType = factory(TenderCriteriaAnswerType::class)->create();

        $resp = $this->tenderCriteriaAnswerTypeRepo->delete($tenderCriteriaAnswerType->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderCriteriaAnswerType::find($tenderCriteriaAnswerType->id), 'TenderCriteriaAnswerType should not exist in DB');
    }
}
