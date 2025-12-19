<?php

use App\Models\ErpAddress;
use App\Repositories\ErpAddressRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErpAddressRepositoryTest extends TestCase
{
    use MakeErpAddressTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpAddressRepository
     */
    protected $erpAddressRepo;

    public function setUp()
    {
        parent::setUp();
        $this->erpAddressRepo = App::make(ErpAddressRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateErpAddress()
    {
        $erpAddress = $this->fakeErpAddressData();
        $createdErpAddress = $this->erpAddressRepo->create($erpAddress);
        $createdErpAddress = $createdErpAddress->toArray();
        $this->assertArrayHasKey('id', $createdErpAddress);
        $this->assertNotNull($createdErpAddress['id'], 'Created ErpAddress must have id specified');
        $this->assertNotNull(ErpAddress::find($createdErpAddress['id']), 'ErpAddress with given id must be in DB');
        $this->assertModelData($erpAddress, $createdErpAddress);
    }

    /**
     * @test read
     */
    public function testReadErpAddress()
    {
        $erpAddress = $this->makeErpAddress();
        $dbErpAddress = $this->erpAddressRepo->find($erpAddress->id);
        $dbErpAddress = $dbErpAddress->toArray();
        $this->assertModelData($erpAddress->toArray(), $dbErpAddress);
    }

    /**
     * @test update
     */
    public function testUpdateErpAddress()
    {
        $erpAddress = $this->makeErpAddress();
        $fakeErpAddress = $this->fakeErpAddressData();
        $updatedErpAddress = $this->erpAddressRepo->update($fakeErpAddress, $erpAddress->id);
        $this->assertModelData($fakeErpAddress, $updatedErpAddress->toArray());
        $dbErpAddress = $this->erpAddressRepo->find($erpAddress->id);
        $this->assertModelData($fakeErpAddress, $dbErpAddress->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteErpAddress()
    {
        $erpAddress = $this->makeErpAddress();
        $resp = $this->erpAddressRepo->delete($erpAddress->id);
        $this->assertTrue($resp);
        $this->assertNull(ErpAddress::find($erpAddress->id), 'ErpAddress should not exist in DB');
    }
}
