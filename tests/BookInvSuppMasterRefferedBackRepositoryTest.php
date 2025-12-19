<?php

use App\Models\BookInvSuppMasterRefferedBack;
use App\Repositories\BookInvSuppMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppMasterRefferedBackRepositoryTest extends TestCase
{
    use MakeBookInvSuppMasterRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BookInvSuppMasterRefferedBackRepository
     */
    protected $bookInvSuppMasterRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bookInvSuppMasterRefferedBackRepo = App::make(BookInvSuppMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->fakeBookInvSuppMasterRefferedBackData();
        $createdBookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepo->create($bookInvSuppMasterRefferedBack);
        $createdBookInvSuppMasterRefferedBack = $createdBookInvSuppMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBookInvSuppMasterRefferedBack);
        $this->assertNotNull($createdBookInvSuppMasterRefferedBack['id'], 'Created BookInvSuppMasterRefferedBack must have id specified');
        $this->assertNotNull(BookInvSuppMasterRefferedBack::find($createdBookInvSuppMasterRefferedBack['id']), 'BookInvSuppMasterRefferedBack with given id must be in DB');
        $this->assertModelData($bookInvSuppMasterRefferedBack, $createdBookInvSuppMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->makeBookInvSuppMasterRefferedBack();
        $dbBookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepo->find($bookInvSuppMasterRefferedBack->id);
        $dbBookInvSuppMasterRefferedBack = $dbBookInvSuppMasterRefferedBack->toArray();
        $this->assertModelData($bookInvSuppMasterRefferedBack->toArray(), $dbBookInvSuppMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->makeBookInvSuppMasterRefferedBack();
        $fakeBookInvSuppMasterRefferedBack = $this->fakeBookInvSuppMasterRefferedBackData();
        $updatedBookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepo->update($fakeBookInvSuppMasterRefferedBack, $bookInvSuppMasterRefferedBack->id);
        $this->assertModelData($fakeBookInvSuppMasterRefferedBack, $updatedBookInvSuppMasterRefferedBack->toArray());
        $dbBookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepo->find($bookInvSuppMasterRefferedBack->id);
        $this->assertModelData($fakeBookInvSuppMasterRefferedBack, $dbBookInvSuppMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->makeBookInvSuppMasterRefferedBack();
        $resp = $this->bookInvSuppMasterRefferedBackRepo->delete($bookInvSuppMasterRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(BookInvSuppMasterRefferedBack::find($bookInvSuppMasterRefferedBack->id), 'BookInvSuppMasterRefferedBack should not exist in DB');
    }
}
