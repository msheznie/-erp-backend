<?php namespace Tests\Repositories;

use App\Models\ErpAttributesDropdown;
use App\Repositories\ErpAttributesDropdownRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpAttributesDropdownRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpAttributesDropdownRepository
     */
    protected $erpAttributesDropdownRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpAttributesDropdownRepo = \App::make(ErpAttributesDropdownRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->make()->toArray();

        $createdErpAttributesDropdown = $this->erpAttributesDropdownRepo->create($erpAttributesDropdown);

        $createdErpAttributesDropdown = $createdErpAttributesDropdown->toArray();
        $this->assertArrayHasKey('id', $createdErpAttributesDropdown);
        $this->assertNotNull($createdErpAttributesDropdown['id'], 'Created ErpAttributesDropdown must have id specified');
        $this->assertNotNull(ErpAttributesDropdown::find($createdErpAttributesDropdown['id']), 'ErpAttributesDropdown with given id must be in DB');
        $this->assertModelData($erpAttributesDropdown, $createdErpAttributesDropdown);
    }

    /**
     * @test read
     */
    public function test_read_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->create();

        $dbErpAttributesDropdown = $this->erpAttributesDropdownRepo->find($erpAttributesDropdown->id);

        $dbErpAttributesDropdown = $dbErpAttributesDropdown->toArray();
        $this->assertModelData($erpAttributesDropdown->toArray(), $dbErpAttributesDropdown);
    }

    /**
     * @test update
     */
    public function test_update_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->create();
        $fakeErpAttributesDropdown = factory(ErpAttributesDropdown::class)->make()->toArray();

        $updatedErpAttributesDropdown = $this->erpAttributesDropdownRepo->update($fakeErpAttributesDropdown, $erpAttributesDropdown->id);

        $this->assertModelData($fakeErpAttributesDropdown, $updatedErpAttributesDropdown->toArray());
        $dbErpAttributesDropdown = $this->erpAttributesDropdownRepo->find($erpAttributesDropdown->id);
        $this->assertModelData($fakeErpAttributesDropdown, $dbErpAttributesDropdown->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_attributes_dropdown()
    {
        $erpAttributesDropdown = factory(ErpAttributesDropdown::class)->create();

        $resp = $this->erpAttributesDropdownRepo->delete($erpAttributesDropdown->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpAttributesDropdown::find($erpAttributesDropdown->id), 'ErpAttributesDropdown should not exist in DB');
    }
}
