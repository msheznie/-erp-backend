<?php namespace Tests\Repositories;

use App\Models\ClientPerformaAppType;
use App\Repositories\ClientPerformaAppTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeClientPerformaAppTypeTrait;
use Tests\ApiTestTrait;

class ClientPerformaAppTypeRepositoryTest extends TestCase
{
    use MakeClientPerformaAppTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ClientPerformaAppTypeRepository
     */
    protected $clientPerformaAppTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->clientPerformaAppTypeRepo = \App::make(ClientPerformaAppTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_client_performa_app_type()
    {
        $clientPerformaAppType = $this->fakeClientPerformaAppTypeData();
        $createdClientPerformaAppType = $this->clientPerformaAppTypeRepo->create($clientPerformaAppType);
        $createdClientPerformaAppType = $createdClientPerformaAppType->toArray();
        $this->assertArrayHasKey('id', $createdClientPerformaAppType);
        $this->assertNotNull($createdClientPerformaAppType['id'], 'Created ClientPerformaAppType must have id specified');
        $this->assertNotNull(ClientPerformaAppType::find($createdClientPerformaAppType['id']), 'ClientPerformaAppType with given id must be in DB');
        $this->assertModelData($clientPerformaAppType, $createdClientPerformaAppType);
    }

    /**
     * @test read
     */
    public function test_read_client_performa_app_type()
    {
        $clientPerformaAppType = $this->makeClientPerformaAppType();
        $dbClientPerformaAppType = $this->clientPerformaAppTypeRepo->find($clientPerformaAppType->id);
        $dbClientPerformaAppType = $dbClientPerformaAppType->toArray();
        $this->assertModelData($clientPerformaAppType->toArray(), $dbClientPerformaAppType);
    }

    /**
     * @test update
     */
    public function test_update_client_performa_app_type()
    {
        $clientPerformaAppType = $this->makeClientPerformaAppType();
        $fakeClientPerformaAppType = $this->fakeClientPerformaAppTypeData();
        $updatedClientPerformaAppType = $this->clientPerformaAppTypeRepo->update($fakeClientPerformaAppType, $clientPerformaAppType->id);
        $this->assertModelData($fakeClientPerformaAppType, $updatedClientPerformaAppType->toArray());
        $dbClientPerformaAppType = $this->clientPerformaAppTypeRepo->find($clientPerformaAppType->id);
        $this->assertModelData($fakeClientPerformaAppType, $dbClientPerformaAppType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_client_performa_app_type()
    {
        $clientPerformaAppType = $this->makeClientPerformaAppType();
        $resp = $this->clientPerformaAppTypeRepo->delete($clientPerformaAppType->id);
        $this->assertTrue($resp);
        $this->assertNull(ClientPerformaAppType::find($clientPerformaAppType->id), 'ClientPerformaAppType should not exist in DB');
    }
}
