<?php

use App\Models\UnbilledGrvGroupBy;
use App\Repositories\UnbilledGrvGroupByRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnbilledGrvGroupByRepositoryTest extends TestCase
{
    use MakeUnbilledGrvGroupByTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UnbilledGrvGroupByRepository
     */
    protected $unbilledGrvGroupByRepo;

    public function setUp()
    {
        parent::setUp();
        $this->unbilledGrvGroupByRepo = App::make(UnbilledGrvGroupByRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->fakeUnbilledGrvGroupByData();
        $createdUnbilledGrvGroupBy = $this->unbilledGrvGroupByRepo->create($unbilledGrvGroupBy);
        $createdUnbilledGrvGroupBy = $createdUnbilledGrvGroupBy->toArray();
        $this->assertArrayHasKey('id', $createdUnbilledGrvGroupBy);
        $this->assertNotNull($createdUnbilledGrvGroupBy['id'], 'Created UnbilledGrvGroupBy must have id specified');
        $this->assertNotNull(UnbilledGrvGroupBy::find($createdUnbilledGrvGroupBy['id']), 'UnbilledGrvGroupBy with given id must be in DB');
        $this->assertModelData($unbilledGrvGroupBy, $createdUnbilledGrvGroupBy);
    }

    /**
     * @test read
     */
    public function testReadUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->makeUnbilledGrvGroupBy();
        $dbUnbilledGrvGroupBy = $this->unbilledGrvGroupByRepo->find($unbilledGrvGroupBy->id);
        $dbUnbilledGrvGroupBy = $dbUnbilledGrvGroupBy->toArray();
        $this->assertModelData($unbilledGrvGroupBy->toArray(), $dbUnbilledGrvGroupBy);
    }

    /**
     * @test update
     */
    public function testUpdateUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->makeUnbilledGrvGroupBy();
        $fakeUnbilledGrvGroupBy = $this->fakeUnbilledGrvGroupByData();
        $updatedUnbilledGrvGroupBy = $this->unbilledGrvGroupByRepo->update($fakeUnbilledGrvGroupBy, $unbilledGrvGroupBy->id);
        $this->assertModelData($fakeUnbilledGrvGroupBy, $updatedUnbilledGrvGroupBy->toArray());
        $dbUnbilledGrvGroupBy = $this->unbilledGrvGroupByRepo->find($unbilledGrvGroupBy->id);
        $this->assertModelData($fakeUnbilledGrvGroupBy, $dbUnbilledGrvGroupBy->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->makeUnbilledGrvGroupBy();
        $resp = $this->unbilledGrvGroupByRepo->delete($unbilledGrvGroupBy->id);
        $this->assertTrue($resp);
        $this->assertNull(UnbilledGrvGroupBy::find($unbilledGrvGroupBy->id), 'UnbilledGrvGroupBy should not exist in DB');
    }
}
