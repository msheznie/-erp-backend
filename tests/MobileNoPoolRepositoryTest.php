<?php namespace Tests\Repositories;

use App\Models\MobileNoPool;
use App\Repositories\MobileNoPoolRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MobileNoPoolRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MobileNoPoolRepository
     */
    protected $mobileNoPoolRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mobileNoPoolRepo = \App::make(MobileNoPoolRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->make()->toArray();

        $createdMobileNoPool = $this->mobileNoPoolRepo->create($mobileNoPool);

        $createdMobileNoPool = $createdMobileNoPool->toArray();
        $this->assertArrayHasKey('id', $createdMobileNoPool);
        $this->assertNotNull($createdMobileNoPool['id'], 'Created MobileNoPool must have id specified');
        $this->assertNotNull(MobileNoPool::find($createdMobileNoPool['id']), 'MobileNoPool with given id must be in DB');
        $this->assertModelData($mobileNoPool, $createdMobileNoPool);
    }

    /**
     * @test read
     */
    public function test_read_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->create();

        $dbMobileNoPool = $this->mobileNoPoolRepo->find($mobileNoPool->id);

        $dbMobileNoPool = $dbMobileNoPool->toArray();
        $this->assertModelData($mobileNoPool->toArray(), $dbMobileNoPool);
    }

    /**
     * @test update
     */
    public function test_update_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->create();
        $fakeMobileNoPool = factory(MobileNoPool::class)->make()->toArray();

        $updatedMobileNoPool = $this->mobileNoPoolRepo->update($fakeMobileNoPool, $mobileNoPool->id);

        $this->assertModelData($fakeMobileNoPool, $updatedMobileNoPool->toArray());
        $dbMobileNoPool = $this->mobileNoPoolRepo->find($mobileNoPool->id);
        $this->assertModelData($fakeMobileNoPool, $dbMobileNoPool->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mobile_no_pool()
    {
        $mobileNoPool = factory(MobileNoPool::class)->create();

        $resp = $this->mobileNoPoolRepo->delete($mobileNoPool->id);

        $this->assertTrue($resp);
        $this->assertNull(MobileNoPool::find($mobileNoPool->id), 'MobileNoPool should not exist in DB');
    }
}
