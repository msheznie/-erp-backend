<?php

use App\Models\BookInvSuppMaster;
use App\Repositories\BookInvSuppMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppMasterRepositoryTest extends TestCase
{
    use MakeBookInvSuppMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BookInvSuppMasterRepository
     */
    protected $bookInvSuppMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bookInvSuppMasterRepo = App::make(BookInvSuppMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->fakeBookInvSuppMasterData();
        $createdBookInvSuppMaster = $this->bookInvSuppMasterRepo->create($bookInvSuppMaster);
        $createdBookInvSuppMaster = $createdBookInvSuppMaster->toArray();
        $this->assertArrayHasKey('id', $createdBookInvSuppMaster);
        $this->assertNotNull($createdBookInvSuppMaster['id'], 'Created BookInvSuppMaster must have id specified');
        $this->assertNotNull(BookInvSuppMaster::find($createdBookInvSuppMaster['id']), 'BookInvSuppMaster with given id must be in DB');
        $this->assertModelData($bookInvSuppMaster, $createdBookInvSuppMaster);
    }

    /**
     * @test read
     */
    public function testReadBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->makeBookInvSuppMaster();
        $dbBookInvSuppMaster = $this->bookInvSuppMasterRepo->find($bookInvSuppMaster->id);
        $dbBookInvSuppMaster = $dbBookInvSuppMaster->toArray();
        $this->assertModelData($bookInvSuppMaster->toArray(), $dbBookInvSuppMaster);
    }

    /**
     * @test update
     */
    public function testUpdateBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->makeBookInvSuppMaster();
        $fakeBookInvSuppMaster = $this->fakeBookInvSuppMasterData();
        $updatedBookInvSuppMaster = $this->bookInvSuppMasterRepo->update($fakeBookInvSuppMaster, $bookInvSuppMaster->id);
        $this->assertModelData($fakeBookInvSuppMaster, $updatedBookInvSuppMaster->toArray());
        $dbBookInvSuppMaster = $this->bookInvSuppMasterRepo->find($bookInvSuppMaster->id);
        $this->assertModelData($fakeBookInvSuppMaster, $dbBookInvSuppMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->makeBookInvSuppMaster();
        $resp = $this->bookInvSuppMasterRepo->delete($bookInvSuppMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(BookInvSuppMaster::find($bookInvSuppMaster->id), 'BookInvSuppMaster should not exist in DB');
    }
}
