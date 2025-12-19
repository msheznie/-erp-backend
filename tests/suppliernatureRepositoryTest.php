<?php

use App\Models\suppliernature;
use App\Repositories\suppliernatureRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class suppliernatureRepositoryTest extends TestCase
{
    use MakesuppliernatureTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var suppliernatureRepository
     */
    protected $suppliernatureRepo;

    public function setUp()
    {
        parent::setUp();
        $this->suppliernatureRepo = App::make(suppliernatureRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatesuppliernature()
    {
        $suppliernature = $this->fakesuppliernatureData();
        $createdsuppliernature = $this->suppliernatureRepo->create($suppliernature);
        $createdsuppliernature = $createdsuppliernature->toArray();
        $this->assertArrayHasKey('id', $createdsuppliernature);
        $this->assertNotNull($createdsuppliernature['id'], 'Created suppliernature must have id specified');
        $this->assertNotNull(suppliernature::find($createdsuppliernature['id']), 'suppliernature with given id must be in DB');
        $this->assertModelData($suppliernature, $createdsuppliernature);
    }

    /**
     * @test read
     */
    public function testReadsuppliernature()
    {
        $suppliernature = $this->makesuppliernature();
        $dbsuppliernature = $this->suppliernatureRepo->find($suppliernature->id);
        $dbsuppliernature = $dbsuppliernature->toArray();
        $this->assertModelData($suppliernature->toArray(), $dbsuppliernature);
    }

    /**
     * @test update
     */
    public function testUpdatesuppliernature()
    {
        $suppliernature = $this->makesuppliernature();
        $fakesuppliernature = $this->fakesuppliernatureData();
        $updatedsuppliernature = $this->suppliernatureRepo->update($fakesuppliernature, $suppliernature->id);
        $this->assertModelData($fakesuppliernature, $updatedsuppliernature->toArray());
        $dbsuppliernature = $this->suppliernatureRepo->find($suppliernature->id);
        $this->assertModelData($fakesuppliernature, $dbsuppliernature->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletesuppliernature()
    {
        $suppliernature = $this->makesuppliernature();
        $resp = $this->suppliernatureRepo->delete($suppliernature->id);
        $this->assertTrue($resp);
        $this->assertNull(suppliernature::find($suppliernature->id), 'suppliernature should not exist in DB');
    }
}
