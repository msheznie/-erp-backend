<?php namespace Tests\Repositories;

use App\Models\TenderType;
use App\Repositories\TenderTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderTypeRepository
     */
    protected $tenderTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderTypeRepo = \App::make(TenderTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_type()
    {
        $tenderType = factory(TenderType::class)->make()->toArray();

        $createdTenderType = $this->tenderTypeRepo->create($tenderType);

        $createdTenderType = $createdTenderType->toArray();
        $this->assertArrayHasKey('id', $createdTenderType);
        $this->assertNotNull($createdTenderType['id'], 'Created TenderType must have id specified');
        $this->assertNotNull(TenderType::find($createdTenderType['id']), 'TenderType with given id must be in DB');
        $this->assertModelData($tenderType, $createdTenderType);
    }

    /**
     * @test read
     */
    public function test_read_tender_type()
    {
        $tenderType = factory(TenderType::class)->create();

        $dbTenderType = $this->tenderTypeRepo->find($tenderType->id);

        $dbTenderType = $dbTenderType->toArray();
        $this->assertModelData($tenderType->toArray(), $dbTenderType);
    }

    /**
     * @test update
     */
    public function test_update_tender_type()
    {
        $tenderType = factory(TenderType::class)->create();
        $fakeTenderType = factory(TenderType::class)->make()->toArray();

        $updatedTenderType = $this->tenderTypeRepo->update($fakeTenderType, $tenderType->id);

        $this->assertModelData($fakeTenderType, $updatedTenderType->toArray());
        $dbTenderType = $this->tenderTypeRepo->find($tenderType->id);
        $this->assertModelData($fakeTenderType, $dbTenderType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_type()
    {
        $tenderType = factory(TenderType::class)->create();

        $resp = $this->tenderTypeRepo->delete($tenderType->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderType::find($tenderType->id), 'TenderType should not exist in DB');
    }
}
