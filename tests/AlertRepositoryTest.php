<?php

use App\Models\Alert;
use App\Repositories\AlertRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlertRepositoryTest extends TestCase
{
    use MakeAlertTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AlertRepository
     */
    protected $alertRepo;

    public function setUp()
    {
        parent::setUp();
        $this->alertRepo = App::make(AlertRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAlert()
    {
        $alert = $this->fakeAlertData();
        $createdAlert = $this->alertRepo->create($alert);
        $createdAlert = $createdAlert->toArray();
        $this->assertArrayHasKey('id', $createdAlert);
        $this->assertNotNull($createdAlert['id'], 'Created Alert must have id specified');
        $this->assertNotNull(Alert::find($createdAlert['id']), 'Alert with given id must be in DB');
        $this->assertModelData($alert, $createdAlert);
    }

    /**
     * @test read
     */
    public function testReadAlert()
    {
        $alert = $this->makeAlert();
        $dbAlert = $this->alertRepo->find($alert->id);
        $dbAlert = $dbAlert->toArray();
        $this->assertModelData($alert->toArray(), $dbAlert);
    }

    /**
     * @test update
     */
    public function testUpdateAlert()
    {
        $alert = $this->makeAlert();
        $fakeAlert = $this->fakeAlertData();
        $updatedAlert = $this->alertRepo->update($fakeAlert, $alert->id);
        $this->assertModelData($fakeAlert, $updatedAlert->toArray());
        $dbAlert = $this->alertRepo->find($alert->id);
        $this->assertModelData($fakeAlert, $dbAlert->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAlert()
    {
        $alert = $this->makeAlert();
        $resp = $this->alertRepo->delete($alert->id);
        $this->assertTrue($resp);
        $this->assertNull(Alert::find($alert->id), 'Alert should not exist in DB');
    }
}
