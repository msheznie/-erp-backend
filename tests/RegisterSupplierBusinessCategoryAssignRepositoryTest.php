<?php namespace Tests\Repositories;

use App\Models\RegisterSupplierBusinessCategoryAssign;
use App\Repositories\RegisterSupplierBusinessCategoryAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisterSupplierBusinessCategoryAssignRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisterSupplierBusinessCategoryAssignRepository
     */
    protected $registerSupplierBusinessCategoryAssignRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registerSupplierBusinessCategoryAssignRepo = \App::make(RegisterSupplierBusinessCategoryAssignRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->make()->toArray();

        $createdRegisterSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepo->create($registerSupplierBusinessCategoryAssign);

        $createdRegisterSupplierBusinessCategoryAssign = $createdRegisterSupplierBusinessCategoryAssign->toArray();
        $this->assertArrayHasKey('id', $createdRegisterSupplierBusinessCategoryAssign);
        $this->assertNotNull($createdRegisterSupplierBusinessCategoryAssign['id'], 'Created RegisterSupplierBusinessCategoryAssign must have id specified');
        $this->assertNotNull(RegisterSupplierBusinessCategoryAssign::find($createdRegisterSupplierBusinessCategoryAssign['id']), 'RegisterSupplierBusinessCategoryAssign with given id must be in DB');
        $this->assertModelData($registerSupplierBusinessCategoryAssign, $createdRegisterSupplierBusinessCategoryAssign);
    }

    /**
     * @test read
     */
    public function test_read_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->create();

        $dbRegisterSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepo->find($registerSupplierBusinessCategoryAssign->id);

        $dbRegisterSupplierBusinessCategoryAssign = $dbRegisterSupplierBusinessCategoryAssign->toArray();
        $this->assertModelData($registerSupplierBusinessCategoryAssign->toArray(), $dbRegisterSupplierBusinessCategoryAssign);
    }

    /**
     * @test update
     */
    public function test_update_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->create();
        $fakeRegisterSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->make()->toArray();

        $updatedRegisterSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepo->update($fakeRegisterSupplierBusinessCategoryAssign, $registerSupplierBusinessCategoryAssign->id);

        $this->assertModelData($fakeRegisterSupplierBusinessCategoryAssign, $updatedRegisterSupplierBusinessCategoryAssign->toArray());
        $dbRegisterSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepo->find($registerSupplierBusinessCategoryAssign->id);
        $this->assertModelData($fakeRegisterSupplierBusinessCategoryAssign, $dbRegisterSupplierBusinessCategoryAssign->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_register_supplier_business_category_assign()
    {
        $registerSupplierBusinessCategoryAssign = factory(RegisterSupplierBusinessCategoryAssign::class)->create();

        $resp = $this->registerSupplierBusinessCategoryAssignRepo->delete($registerSupplierBusinessCategoryAssign->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisterSupplierBusinessCategoryAssign::find($registerSupplierBusinessCategoryAssign->id), 'RegisterSupplierBusinessCategoryAssign should not exist in DB');
    }
}
