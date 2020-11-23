<?php namespace Tests\Repositories;

use App\Models\RegisteredSupplierContactDetail;
use App\Repositories\RegisteredSupplierContactDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisteredSupplierContactDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisteredSupplierContactDetailRepository
     */
    protected $registeredSupplierContactDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registeredSupplierContactDetailRepo = \App::make(RegisteredSupplierContactDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->make()->toArray();

        $createdRegisteredSupplierContactDetail = $this->registeredSupplierContactDetailRepo->create($registeredSupplierContactDetail);

        $createdRegisteredSupplierContactDetail = $createdRegisteredSupplierContactDetail->toArray();
        $this->assertArrayHasKey('id', $createdRegisteredSupplierContactDetail);
        $this->assertNotNull($createdRegisteredSupplierContactDetail['id'], 'Created RegisteredSupplierContactDetail must have id specified');
        $this->assertNotNull(RegisteredSupplierContactDetail::find($createdRegisteredSupplierContactDetail['id']), 'RegisteredSupplierContactDetail with given id must be in DB');
        $this->assertModelData($registeredSupplierContactDetail, $createdRegisteredSupplierContactDetail);
    }

    /**
     * @test read
     */
    public function test_read_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->create();

        $dbRegisteredSupplierContactDetail = $this->registeredSupplierContactDetailRepo->find($registeredSupplierContactDetail->id);

        $dbRegisteredSupplierContactDetail = $dbRegisteredSupplierContactDetail->toArray();
        $this->assertModelData($registeredSupplierContactDetail->toArray(), $dbRegisteredSupplierContactDetail);
    }

    /**
     * @test update
     */
    public function test_update_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->create();
        $fakeRegisteredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->make()->toArray();

        $updatedRegisteredSupplierContactDetail = $this->registeredSupplierContactDetailRepo->update($fakeRegisteredSupplierContactDetail, $registeredSupplierContactDetail->id);

        $this->assertModelData($fakeRegisteredSupplierContactDetail, $updatedRegisteredSupplierContactDetail->toArray());
        $dbRegisteredSupplierContactDetail = $this->registeredSupplierContactDetailRepo->find($registeredSupplierContactDetail->id);
        $this->assertModelData($fakeRegisteredSupplierContactDetail, $dbRegisteredSupplierContactDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_registered_supplier_contact_detail()
    {
        $registeredSupplierContactDetail = factory(RegisteredSupplierContactDetail::class)->create();

        $resp = $this->registeredSupplierContactDetailRepo->delete($registeredSupplierContactDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisteredSupplierContactDetail::find($registeredSupplierContactDetail->id), 'RegisteredSupplierContactDetail should not exist in DB');
    }
}
