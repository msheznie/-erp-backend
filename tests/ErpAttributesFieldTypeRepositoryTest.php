<?php namespace Tests\Repositories;

use App\Models\ErpAttributesFieldType;
use App\Repositories\ErpAttributesFieldTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpAttributesFieldTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpAttributesFieldTypeRepository
     */
    protected $erpAttributesFieldTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpAttributesFieldTypeRepo = \App::make(ErpAttributesFieldTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->make()->toArray();

        $createdErpAttributesFieldType = $this->erpAttributesFieldTypeRepo->create($erpAttributesFieldType);

        $createdErpAttributesFieldType = $createdErpAttributesFieldType->toArray();
        $this->assertArrayHasKey('id', $createdErpAttributesFieldType);
        $this->assertNotNull($createdErpAttributesFieldType['id'], 'Created ErpAttributesFieldType must have id specified');
        $this->assertNotNull(ErpAttributesFieldType::find($createdErpAttributesFieldType['id']), 'ErpAttributesFieldType with given id must be in DB');
        $this->assertModelData($erpAttributesFieldType, $createdErpAttributesFieldType);
    }

    /**
     * @test read
     */
    public function test_read_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->create();

        $dbErpAttributesFieldType = $this->erpAttributesFieldTypeRepo->find($erpAttributesFieldType->id);

        $dbErpAttributesFieldType = $dbErpAttributesFieldType->toArray();
        $this->assertModelData($erpAttributesFieldType->toArray(), $dbErpAttributesFieldType);
    }

    /**
     * @test update
     */
    public function test_update_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->create();
        $fakeErpAttributesFieldType = factory(ErpAttributesFieldType::class)->make()->toArray();

        $updatedErpAttributesFieldType = $this->erpAttributesFieldTypeRepo->update($fakeErpAttributesFieldType, $erpAttributesFieldType->id);

        $this->assertModelData($fakeErpAttributesFieldType, $updatedErpAttributesFieldType->toArray());
        $dbErpAttributesFieldType = $this->erpAttributesFieldTypeRepo->find($erpAttributesFieldType->id);
        $this->assertModelData($fakeErpAttributesFieldType, $dbErpAttributesFieldType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->create();

        $resp = $this->erpAttributesFieldTypeRepo->delete($erpAttributesFieldType->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpAttributesFieldType::find($erpAttributesFieldType->id), 'ErpAttributesFieldType should not exist in DB');
    }
}
