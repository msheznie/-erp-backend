<?php

use App\Models\BookInvSuppDet;
use App\Repositories\BookInvSuppDetRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppDetRepositoryTest extends TestCase
{
    use MakeBookInvSuppDetTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BookInvSuppDetRepository
     */
    protected $bookInvSuppDetRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bookInvSuppDetRepo = App::make(BookInvSuppDetRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBookInvSuppDet()
    {
        $bookInvSuppDet = $this->fakeBookInvSuppDetData();
        $createdBookInvSuppDet = $this->bookInvSuppDetRepo->create($bookInvSuppDet);
        $createdBookInvSuppDet = $createdBookInvSuppDet->toArray();
        $this->assertArrayHasKey('id', $createdBookInvSuppDet);
        $this->assertNotNull($createdBookInvSuppDet['id'], 'Created BookInvSuppDet must have id specified');
        $this->assertNotNull(BookInvSuppDet::find($createdBookInvSuppDet['id']), 'BookInvSuppDet with given id must be in DB');
        $this->assertModelData($bookInvSuppDet, $createdBookInvSuppDet);
    }

    /**
     * @test read
     */
    public function testReadBookInvSuppDet()
    {
        $bookInvSuppDet = $this->makeBookInvSuppDet();
        $dbBookInvSuppDet = $this->bookInvSuppDetRepo->find($bookInvSuppDet->id);
        $dbBookInvSuppDet = $dbBookInvSuppDet->toArray();
        $this->assertModelData($bookInvSuppDet->toArray(), $dbBookInvSuppDet);
    }

    /**
     * @test update
     */
    public function testUpdateBookInvSuppDet()
    {
        $bookInvSuppDet = $this->makeBookInvSuppDet();
        $fakeBookInvSuppDet = $this->fakeBookInvSuppDetData();
        $updatedBookInvSuppDet = $this->bookInvSuppDetRepo->update($fakeBookInvSuppDet, $bookInvSuppDet->id);
        $this->assertModelData($fakeBookInvSuppDet, $updatedBookInvSuppDet->toArray());
        $dbBookInvSuppDet = $this->bookInvSuppDetRepo->find($bookInvSuppDet->id);
        $this->assertModelData($fakeBookInvSuppDet, $dbBookInvSuppDet->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBookInvSuppDet()
    {
        $bookInvSuppDet = $this->makeBookInvSuppDet();
        $resp = $this->bookInvSuppDetRepo->delete($bookInvSuppDet->id);
        $this->assertTrue($resp);
        $this->assertNull(BookInvSuppDet::find($bookInvSuppDet->id), 'BookInvSuppDet should not exist in DB');
    }
}
