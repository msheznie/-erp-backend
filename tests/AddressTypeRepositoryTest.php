<?php

use App\Models\AddressType;
use App\Repositories\AddressTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressTypeRepositoryTest extends TestCase
{
    use MakeAddressTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AddressTypeRepository
     */
    protected $addressTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->addressTypeRepo = App::make(AddressTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAddressType()
    {
        $addressType = $this->fakeAddressTypeData();
        $createdAddressType = $this->addressTypeRepo->create($addressType);
        $createdAddressType = $createdAddressType->toArray();
        $this->assertArrayHasKey('id', $createdAddressType);
        $this->assertNotNull($createdAddressType['id'], 'Created AddressType must have id specified');
        $this->assertNotNull(AddressType::find($createdAddressType['id']), 'AddressType with given id must be in DB');
        $this->assertModelData($addressType, $createdAddressType);
    }

    /**
     * @test read
     */
    public function testReadAddressType()
    {
        $addressType = $this->makeAddressType();
        $dbAddressType = $this->addressTypeRepo->find($addressType->id);
        $dbAddressType = $dbAddressType->toArray();
        $this->assertModelData($addressType->toArray(), $dbAddressType);
    }

    /**
     * @test update
     */
    public function testUpdateAddressType()
    {
        $addressType = $this->makeAddressType();
        $fakeAddressType = $this->fakeAddressTypeData();
        $updatedAddressType = $this->addressTypeRepo->update($fakeAddressType, $addressType->id);
        $this->assertModelData($fakeAddressType, $updatedAddressType->toArray());
        $dbAddressType = $this->addressTypeRepo->find($addressType->id);
        $this->assertModelData($fakeAddressType, $dbAddressType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAddressType()
    {
        $addressType = $this->makeAddressType();
        $resp = $this->addressTypeRepo->delete($addressType->id);
        $this->assertTrue($resp);
        $this->assertNull(AddressType::find($addressType->id), 'AddressType should not exist in DB');
    }
}
