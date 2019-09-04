<?php namespace Tests\Repositories;

use App\Models\Religion;
use App\Repositories\ReligionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeReligionTrait;
use Tests\ApiTestTrait;

class ReligionRepositoryTest extends TestCase
{
    use MakeReligionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReligionRepository
     */
    protected $religionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->religionRepo = \App::make(ReligionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_religion()
    {
        $religion = $this->fakeReligionData();
        $createdReligion = $this->religionRepo->create($religion);
        $createdReligion = $createdReligion->toArray();
        $this->assertArrayHasKey('id', $createdReligion);
        $this->assertNotNull($createdReligion['id'], 'Created Religion must have id specified');
        $this->assertNotNull(Religion::find($createdReligion['id']), 'Religion with given id must be in DB');
        $this->assertModelData($religion, $createdReligion);
    }

    /**
     * @test read
     */
    public function test_read_religion()
    {
        $religion = $this->makeReligion();
        $dbReligion = $this->religionRepo->find($religion->id);
        $dbReligion = $dbReligion->toArray();
        $this->assertModelData($religion->toArray(), $dbReligion);
    }

    /**
     * @test update
     */
    public function test_update_religion()
    {
        $religion = $this->makeReligion();
        $fakeReligion = $this->fakeReligionData();
        $updatedReligion = $this->religionRepo->update($fakeReligion, $religion->id);
        $this->assertModelData($fakeReligion, $updatedReligion->toArray());
        $dbReligion = $this->religionRepo->find($religion->id);
        $this->assertModelData($fakeReligion, $dbReligion->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_religion()
    {
        $religion = $this->makeReligion();
        $resp = $this->religionRepo->delete($religion->id);
        $this->assertTrue($resp);
        $this->assertNull(Religion::find($religion->id), 'Religion should not exist in DB');
    }
}
