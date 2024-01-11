<?php namespace Tests\Repositories;

use App\Models\RegisterSupplierSubcategoryAssign;
use App\Repositories\RegisterSupplierSubcategoryAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisterSupplierSubcategoryAssignRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisterSupplierSubcategoryAssignRepository
     */
    protected $registerSupplierSubcategoryAssignRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registerSupplierSubcategoryAssignRepo = \App::make(RegisterSupplierSubcategoryAssignRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->make()->toArray();

        $createdRegisterSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepo->create($registerSupplierSubcategoryAssign);

        $createdRegisterSupplierSubcategoryAssign = $createdRegisterSupplierSubcategoryAssign->toArray();
        $this->assertArrayHasKey('id', $createdRegisterSupplierSubcategoryAssign);
        $this->assertNotNull($createdRegisterSupplierSubcategoryAssign['id'], 'Created RegisterSupplierSubcategoryAssign must have id specified');
        $this->assertNotNull(RegisterSupplierSubcategoryAssign::find($createdRegisterSupplierSubcategoryAssign['id']), 'RegisterSupplierSubcategoryAssign with given id must be in DB');
        $this->assertModelData($registerSupplierSubcategoryAssign, $createdRegisterSupplierSubcategoryAssign);
    }

    /**
     * @test read
     */
    public function test_read_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->create();

        $dbRegisterSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepo->find($registerSupplierSubcategoryAssign->id);

        $dbRegisterSupplierSubcategoryAssign = $dbRegisterSupplierSubcategoryAssign->toArray();
        $this->assertModelData($registerSupplierSubcategoryAssign->toArray(), $dbRegisterSupplierSubcategoryAssign);
    }

    /**
     * @test update
     */
    public function test_update_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->create();
        $fakeRegisterSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->make()->toArray();

        $updatedRegisterSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepo->update($fakeRegisterSupplierSubcategoryAssign, $registerSupplierSubcategoryAssign->id);

        $this->assertModelData($fakeRegisterSupplierSubcategoryAssign, $updatedRegisterSupplierSubcategoryAssign->toArray());
        $dbRegisterSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepo->find($registerSupplierSubcategoryAssign->id);
        $this->assertModelData($fakeRegisterSupplierSubcategoryAssign, $dbRegisterSupplierSubcategoryAssign->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_register_supplier_subcategory_assign()
    {
        $registerSupplierSubcategoryAssign = factory(RegisterSupplierSubcategoryAssign::class)->create();

        $resp = $this->registerSupplierSubcategoryAssignRepo->delete($registerSupplierSubcategoryAssign->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisterSupplierSubcategoryAssign::find($registerSupplierSubcategoryAssign->id), 'RegisterSupplierSubcategoryAssign should not exist in DB');
    }
}
