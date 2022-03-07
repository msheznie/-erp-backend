<?php namespace Tests\Repositories;

use App\Models\TenderFieldType;
use App\Repositories\TenderFieldTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderFieldTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderFieldTypeRepository
     */
    protected $tenderFieldTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderFieldTypeRepo = \App::make(TenderFieldTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->make()->toArray();

        $createdTenderFieldType = $this->tenderFieldTypeRepo->create($tenderFieldType);

        $createdTenderFieldType = $createdTenderFieldType->toArray();
        $this->assertArrayHasKey('id', $createdTenderFieldType);
        $this->assertNotNull($createdTenderFieldType['id'], 'Created TenderFieldType must have id specified');
        $this->assertNotNull(TenderFieldType::find($createdTenderFieldType['id']), 'TenderFieldType with given id must be in DB');
        $this->assertModelData($tenderFieldType, $createdTenderFieldType);
    }

    /**
     * @test read
     */
    public function test_read_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->create();

        $dbTenderFieldType = $this->tenderFieldTypeRepo->find($tenderFieldType->id);

        $dbTenderFieldType = $dbTenderFieldType->toArray();
        $this->assertModelData($tenderFieldType->toArray(), $dbTenderFieldType);
    }

    /**
     * @test update
     */
    public function test_update_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->create();
        $fakeTenderFieldType = factory(TenderFieldType::class)->make()->toArray();

        $updatedTenderFieldType = $this->tenderFieldTypeRepo->update($fakeTenderFieldType, $tenderFieldType->id);

        $this->assertModelData($fakeTenderFieldType, $updatedTenderFieldType->toArray());
        $dbTenderFieldType = $this->tenderFieldTypeRepo->find($tenderFieldType->id);
        $this->assertModelData($fakeTenderFieldType, $dbTenderFieldType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->create();

        $resp = $this->tenderFieldTypeRepo->delete($tenderFieldType->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderFieldType::find($tenderFieldType->id), 'TenderFieldType should not exist in DB');
    }
}
