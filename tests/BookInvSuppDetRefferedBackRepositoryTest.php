<?php

use App\Models\BookInvSuppDetRefferedBack;
use App\Repositories\BookInvSuppDetRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppDetRefferedBackRepositoryTest extends TestCase
{
    use MakeBookInvSuppDetRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BookInvSuppDetRefferedBackRepository
     */
    protected $bookInvSuppDetRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bookInvSuppDetRefferedBackRepo = App::make(BookInvSuppDetRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->fakeBookInvSuppDetRefferedBackData();
        $createdBookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepo->create($bookInvSuppDetRefferedBack);
        $createdBookInvSuppDetRefferedBack = $createdBookInvSuppDetRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBookInvSuppDetRefferedBack);
        $this->assertNotNull($createdBookInvSuppDetRefferedBack['id'], 'Created BookInvSuppDetRefferedBack must have id specified');
        $this->assertNotNull(BookInvSuppDetRefferedBack::find($createdBookInvSuppDetRefferedBack['id']), 'BookInvSuppDetRefferedBack with given id must be in DB');
        $this->assertModelData($bookInvSuppDetRefferedBack, $createdBookInvSuppDetRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->makeBookInvSuppDetRefferedBack();
        $dbBookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepo->find($bookInvSuppDetRefferedBack->id);
        $dbBookInvSuppDetRefferedBack = $dbBookInvSuppDetRefferedBack->toArray();
        $this->assertModelData($bookInvSuppDetRefferedBack->toArray(), $dbBookInvSuppDetRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->makeBookInvSuppDetRefferedBack();
        $fakeBookInvSuppDetRefferedBack = $this->fakeBookInvSuppDetRefferedBackData();
        $updatedBookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepo->update($fakeBookInvSuppDetRefferedBack, $bookInvSuppDetRefferedBack->id);
        $this->assertModelData($fakeBookInvSuppDetRefferedBack, $updatedBookInvSuppDetRefferedBack->toArray());
        $dbBookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepo->find($bookInvSuppDetRefferedBack->id);
        $this->assertModelData($fakeBookInvSuppDetRefferedBack, $dbBookInvSuppDetRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->makeBookInvSuppDetRefferedBack();
        $resp = $this->bookInvSuppDetRefferedBackRepo->delete($bookInvSuppDetRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(BookInvSuppDetRefferedBack::find($bookInvSuppDetRefferedBack->id), 'BookInvSuppDetRefferedBack should not exist in DB');
    }
}
