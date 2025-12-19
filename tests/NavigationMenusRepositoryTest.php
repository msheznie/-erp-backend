<?php

use App\Models\NavigationMenus;
use App\Repositories\NavigationMenusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NavigationMenusRepositoryTest extends TestCase
{
    use MakeNavigationMenusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var NavigationMenusRepository
     */
    protected $navigationMenusRepo;

    public function setUp()
    {
        parent::setUp();
        $this->navigationMenusRepo = App::make(NavigationMenusRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateNavigationMenus()
    {
        $navigationMenus = $this->fakeNavigationMenusData();
        $createdNavigationMenus = $this->navigationMenusRepo->create($navigationMenus);
        $createdNavigationMenus = $createdNavigationMenus->toArray();
        $this->assertArrayHasKey('id', $createdNavigationMenus);
        $this->assertNotNull($createdNavigationMenus['id'], 'Created NavigationMenus must have id specified');
        $this->assertNotNull(NavigationMenus::find($createdNavigationMenus['id']), 'NavigationMenus with given id must be in DB');
        $this->assertModelData($navigationMenus, $createdNavigationMenus);
    }

    /**
     * @test read
     */
    public function testReadNavigationMenus()
    {
        $navigationMenus = $this->makeNavigationMenus();
        $dbNavigationMenus = $this->navigationMenusRepo->find($navigationMenus->id);
        $dbNavigationMenus = $dbNavigationMenus->toArray();
        $this->assertModelData($navigationMenus->toArray(), $dbNavigationMenus);
    }

    /**
     * @test update
     */
    public function testUpdateNavigationMenus()
    {
        $navigationMenus = $this->makeNavigationMenus();
        $fakeNavigationMenus = $this->fakeNavigationMenusData();
        $updatedNavigationMenus = $this->navigationMenusRepo->update($fakeNavigationMenus, $navigationMenus->id);
        $this->assertModelData($fakeNavigationMenus, $updatedNavigationMenus->toArray());
        $dbNavigationMenus = $this->navigationMenusRepo->find($navigationMenus->id);
        $this->assertModelData($fakeNavigationMenus, $dbNavigationMenus->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteNavigationMenus()
    {
        $navigationMenus = $this->makeNavigationMenus();
        $resp = $this->navigationMenusRepo->delete($navigationMenus->id);
        $this->assertTrue($resp);
        $this->assertNull(NavigationMenus::find($navigationMenus->id), 'NavigationMenus should not exist in DB');
    }
}
