<?php namespace Tests\Repositories;

use App\Models\ErpAttributesFieldTypeTranslation;
use App\Repositories\ErpAttributesFieldTypeTranslationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpAttributesFieldTypeTranslationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpAttributesFieldTypeTranslationRepository
     */
    protected $erpAttributesFieldTypeTranslationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpAttributesFieldTypeTranslationRepo = \App::make(ErpAttributesFieldTypeTranslationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->make()->toArray();

        $createdErpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepo->create($erpAttributesFieldTypeTranslation);

        $createdErpAttributesFieldTypeTranslation = $createdErpAttributesFieldTypeTranslation->toArray();
        $this->assertArrayHasKey('id', $createdErpAttributesFieldTypeTranslation);
        $this->assertNotNull($createdErpAttributesFieldTypeTranslation['id'], 'Created ErpAttributesFieldTypeTranslation must have id specified');
        $this->assertNotNull(ErpAttributesFieldTypeTranslation::find($createdErpAttributesFieldTypeTranslation['id']), 'ErpAttributesFieldTypeTranslation with given id must be in DB');
        $this->assertModelData($erpAttributesFieldTypeTranslation, $createdErpAttributesFieldTypeTranslation);
    }

    /**
     * @test read
     */
    public function test_read_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->create();

        $dbErpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepo->find($erpAttributesFieldTypeTranslation->id);

        $dbErpAttributesFieldTypeTranslation = $dbErpAttributesFieldTypeTranslation->toArray();
        $this->assertModelData($erpAttributesFieldTypeTranslation->toArray(), $dbErpAttributesFieldTypeTranslation);
    }

    /**
     * @test update
     */
    public function test_update_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->create();
        $fakeErpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->make()->toArray();

        $updatedErpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepo->update($fakeErpAttributesFieldTypeTranslation, $erpAttributesFieldTypeTranslation->id);

        $this->assertModelData($fakeErpAttributesFieldTypeTranslation, $updatedErpAttributesFieldTypeTranslation->toArray());
        $dbErpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepo->find($erpAttributesFieldTypeTranslation->id);
        $this->assertModelData($fakeErpAttributesFieldTypeTranslation, $dbErpAttributesFieldTypeTranslation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->create();

        $resp = $this->erpAttributesFieldTypeTranslationRepo->delete($erpAttributesFieldTypeTranslation->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpAttributesFieldTypeTranslation::find($erpAttributesFieldTypeTranslation->id), 'ErpAttributesFieldTypeTranslation should not exist in DB');
    }
}
