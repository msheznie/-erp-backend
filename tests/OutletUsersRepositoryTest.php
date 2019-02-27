<?php

use App\Models\OutletUsers;
use App\Repositories\OutletUsersRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OutletUsersRepositoryTest extends TestCase
{
    use MakeOutletUsersTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var OutletUsersRepository
     */
    protected $outletUsersRepo;

    public function setUp()
    {
        parent::setUp();
        $this->outletUsersRepo = App::make(OutletUsersRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateOutletUsers()
    {
        $outletUsers = $this->fakeOutletUsersData();
        $createdOutletUsers = $this->outletUsersRepo->create($outletUsers);
        $createdOutletUsers = $createdOutletUsers->toArray();
        $this->assertArrayHasKey('id', $createdOutletUsers);
        $this->assertNotNull($createdOutletUsers['id'], 'Created OutletUsers must have id specified');
        $this->assertNotNull(OutletUsers::find($createdOutletUsers['id']), 'OutletUsers with given id must be in DB');
        $this->assertModelData($outletUsers, $createdOutletUsers);
    }

    /**
     * @test read
     */
    public function testReadOutletUsers()
    {
        $outletUsers = $this->makeOutletUsers();
        $dbOutletUsers = $this->outletUsersRepo->find($outletUsers->id);
        $dbOutletUsers = $dbOutletUsers->toArray();
        $this->assertModelData($outletUsers->toArray(), $dbOutletUsers);
    }

    /**
     * @test update
     */
    public function testUpdateOutletUsers()
    {
        $outletUsers = $this->makeOutletUsers();
        $fakeOutletUsers = $this->fakeOutletUsersData();
        $updatedOutletUsers = $this->outletUsersRepo->update($fakeOutletUsers, $outletUsers->id);
        $this->assertModelData($fakeOutletUsers, $updatedOutletUsers->toArray());
        $dbOutletUsers = $this->outletUsersRepo->find($outletUsers->id);
        $this->assertModelData($fakeOutletUsers, $dbOutletUsers->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteOutletUsers()
    {
        $outletUsers = $this->makeOutletUsers();
        $resp = $this->outletUsersRepo->delete($outletUsers->id);
        $this->assertTrue($resp);
        $this->assertNull(OutletUsers::find($outletUsers->id), 'OutletUsers should not exist in DB');
    }
}
