<?php

use App\Models\TicketMaster;
use App\Repositories\TicketMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketMasterRepositoryTest extends TestCase
{
    use MakeTicketMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TicketMasterRepository
     */
    protected $ticketMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->ticketMasterRepo = App::make(TicketMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTicketMaster()
    {
        $ticketMaster = $this->fakeTicketMasterData();
        $createdTicketMaster = $this->ticketMasterRepo->create($ticketMaster);
        $createdTicketMaster = $createdTicketMaster->toArray();
        $this->assertArrayHasKey('id', $createdTicketMaster);
        $this->assertNotNull($createdTicketMaster['id'], 'Created TicketMaster must have id specified');
        $this->assertNotNull(TicketMaster::find($createdTicketMaster['id']), 'TicketMaster with given id must be in DB');
        $this->assertModelData($ticketMaster, $createdTicketMaster);
    }

    /**
     * @test read
     */
    public function testReadTicketMaster()
    {
        $ticketMaster = $this->makeTicketMaster();
        $dbTicketMaster = $this->ticketMasterRepo->find($ticketMaster->id);
        $dbTicketMaster = $dbTicketMaster->toArray();
        $this->assertModelData($ticketMaster->toArray(), $dbTicketMaster);
    }

    /**
     * @test update
     */
    public function testUpdateTicketMaster()
    {
        $ticketMaster = $this->makeTicketMaster();
        $fakeTicketMaster = $this->fakeTicketMasterData();
        $updatedTicketMaster = $this->ticketMasterRepo->update($fakeTicketMaster, $ticketMaster->id);
        $this->assertModelData($fakeTicketMaster, $updatedTicketMaster->toArray());
        $dbTicketMaster = $this->ticketMasterRepo->find($ticketMaster->id);
        $this->assertModelData($fakeTicketMaster, $dbTicketMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTicketMaster()
    {
        $ticketMaster = $this->makeTicketMaster();
        $resp = $this->ticketMasterRepo->delete($ticketMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(TicketMaster::find($ticketMaster->id), 'TicketMaster should not exist in DB');
    }
}
