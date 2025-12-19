<?php

use App\Models\PoAddons;
use App\Repositories\PoAddonsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoAddonsRepositoryTest extends TestCase
{
    use MakePoAddonsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoAddonsRepository
     */
    protected $poAddonsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->poAddonsRepo = App::make(PoAddonsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePoAddons()
    {
        $poAddons = $this->fakePoAddonsData();
        $createdPoAddons = $this->poAddonsRepo->create($poAddons);
        $createdPoAddons = $createdPoAddons->toArray();
        $this->assertArrayHasKey('id', $createdPoAddons);
        $this->assertNotNull($createdPoAddons['id'], 'Created PoAddons must have id specified');
        $this->assertNotNull(PoAddons::find($createdPoAddons['id']), 'PoAddons with given id must be in DB');
        $this->assertModelData($poAddons, $createdPoAddons);
    }

    /**
     * @test read
     */
    public function testReadPoAddons()
    {
        $poAddons = $this->makePoAddons();
        $dbPoAddons = $this->poAddonsRepo->find($poAddons->id);
        $dbPoAddons = $dbPoAddons->toArray();
        $this->assertModelData($poAddons->toArray(), $dbPoAddons);
    }

    /**
     * @test update
     */
    public function testUpdatePoAddons()
    {
        $poAddons = $this->makePoAddons();
        $fakePoAddons = $this->fakePoAddonsData();
        $updatedPoAddons = $this->poAddonsRepo->update($fakePoAddons, $poAddons->id);
        $this->assertModelData($fakePoAddons, $updatedPoAddons->toArray());
        $dbPoAddons = $this->poAddonsRepo->find($poAddons->id);
        $this->assertModelData($fakePoAddons, $dbPoAddons->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePoAddons()
    {
        $poAddons = $this->makePoAddons();
        $resp = $this->poAddonsRepo->delete($poAddons->id);
        $this->assertTrue($resp);
        $this->assertNull(PoAddons::find($poAddons->id), 'PoAddons should not exist in DB');
    }
}
