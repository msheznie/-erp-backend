<?php namespace Tests\Repositories;

use App\Models\ErpAttributes;
use App\Repositories\ErpAttributesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpAttributesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpAttributesRepository
     */
    protected $erpAttributesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpAttributesRepo = \App::make(ErpAttributesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->make()->toArray();

        $createdErpAttributes = $this->erpAttributesRepo->create($erpAttributes);

        $createdErpAttributes = $createdErpAttributes->toArray();
        $this->assertArrayHasKey('id', $createdErpAttributes);
        $this->assertNotNull($createdErpAttributes['id'], 'Created ErpAttributes must have id specified');
        $this->assertNotNull(ErpAttributes::find($createdErpAttributes['id']), 'ErpAttributes with given id must be in DB');
        $this->assertModelData($erpAttributes, $createdErpAttributes);
    }

    /**
     * @test read
     */
    public function test_read_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->create();

        $dbErpAttributes = $this->erpAttributesRepo->find($erpAttributes->id);

        $dbErpAttributes = $dbErpAttributes->toArray();
        $this->assertModelData($erpAttributes->toArray(), $dbErpAttributes);
    }

    /**
     * @test update
     */
    public function test_update_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->create();
        $fakeErpAttributes = factory(ErpAttributes::class)->make()->toArray();

        $updatedErpAttributes = $this->erpAttributesRepo->update($fakeErpAttributes, $erpAttributes->id);

        $this->assertModelData($fakeErpAttributes, $updatedErpAttributes->toArray());
        $dbErpAttributes = $this->erpAttributesRepo->find($erpAttributes->id);
        $this->assertModelData($fakeErpAttributes, $dbErpAttributes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->create();

        $resp = $this->erpAttributesRepo->delete($erpAttributes->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpAttributes::find($erpAttributes->id), 'ErpAttributes should not exist in DB');
    }
}
