<?php

use App\Models\NavigationUserGroupSetup;
use App\Repositories\NavigationUserGroupSetupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NavigationUserGroupSetupRepositoryTest extends TestCase
{
    use MakeNavigationUserGroupSetupTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var NavigationUserGroupSetupRepository
     */
    protected $navigationUserGroupSetupRepo;

    public function setUp()
    {
        parent::setUp();
        $this->navigationUserGroupSetupRepo = App::make(NavigationUserGroupSetupRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->fakeNavigationUserGroupSetupData();
        $createdNavigationUserGroupSetup = $this->navigationUserGroupSetupRepo->create($navigationUserGroupSetup);
        $createdNavigationUserGroupSetup = $createdNavigationUserGroupSetup->toArray();
        $this->assertArrayHasKey('id', $createdNavigationUserGroupSetup);
        $this->assertNotNull($createdNavigationUserGroupSetup['id'], 'Created NavigationUserGroupSetup must have id specified');
        $this->assertNotNull(NavigationUserGroupSetup::find($createdNavigationUserGroupSetup['id']), 'NavigationUserGroupSetup with given id must be in DB');
        $this->assertModelData($navigationUserGroupSetup, $createdNavigationUserGroupSetup);
    }

    /**
     * @test read
     */
    public function testReadNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->makeNavigationUserGroupSetup();
        $dbNavigationUserGroupSetup = $this->navigationUserGroupSetupRepo->find($navigationUserGroupSetup->id);
        $dbNavigationUserGroupSetup = $dbNavigationUserGroupSetup->toArray();
        $this->assertModelData($navigationUserGroupSetup->toArray(), $dbNavigationUserGroupSetup);
    }

    /**
     * @test update
     */
    public function testUpdateNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->makeNavigationUserGroupSetup();
        $fakeNavigationUserGroupSetup = $this->fakeNavigationUserGroupSetupData();
        $updatedNavigationUserGroupSetup = $this->navigationUserGroupSetupRepo->update($fakeNavigationUserGroupSetup, $navigationUserGroupSetup->id);
        $this->assertModelData($fakeNavigationUserGroupSetup, $updatedNavigationUserGroupSetup->toArray());
        $dbNavigationUserGroupSetup = $this->navigationUserGroupSetupRepo->find($navigationUserGroupSetup->id);
        $this->assertModelData($fakeNavigationUserGroupSetup, $dbNavigationUserGroupSetup->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->makeNavigationUserGroupSetup();
        $resp = $this->navigationUserGroupSetupRepo->delete($navigationUserGroupSetup->id);
        $this->assertTrue($resp);
        $this->assertNull(NavigationUserGroupSetup::find($navigationUserGroupSetup->id), 'NavigationUserGroupSetup should not exist in DB');
    }
}
