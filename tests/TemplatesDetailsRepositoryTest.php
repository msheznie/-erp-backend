<?php

use App\Models\TemplatesDetails;
use App\Repositories\TemplatesDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplatesDetailsRepositoryTest extends TestCase
{
    use MakeTemplatesDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TemplatesDetailsRepository
     */
    protected $templatesDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->templatesDetailsRepo = App::make(TemplatesDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTemplatesDetails()
    {
        $templatesDetails = $this->fakeTemplatesDetailsData();
        $createdTemplatesDetails = $this->templatesDetailsRepo->create($templatesDetails);
        $createdTemplatesDetails = $createdTemplatesDetails->toArray();
        $this->assertArrayHasKey('id', $createdTemplatesDetails);
        $this->assertNotNull($createdTemplatesDetails['id'], 'Created TemplatesDetails must have id specified');
        $this->assertNotNull(TemplatesDetails::find($createdTemplatesDetails['id']), 'TemplatesDetails with given id must be in DB');
        $this->assertModelData($templatesDetails, $createdTemplatesDetails);
    }

    /**
     * @test read
     */
    public function testReadTemplatesDetails()
    {
        $templatesDetails = $this->makeTemplatesDetails();
        $dbTemplatesDetails = $this->templatesDetailsRepo->find($templatesDetails->id);
        $dbTemplatesDetails = $dbTemplatesDetails->toArray();
        $this->assertModelData($templatesDetails->toArray(), $dbTemplatesDetails);
    }

    /**
     * @test update
     */
    public function testUpdateTemplatesDetails()
    {
        $templatesDetails = $this->makeTemplatesDetails();
        $fakeTemplatesDetails = $this->fakeTemplatesDetailsData();
        $updatedTemplatesDetails = $this->templatesDetailsRepo->update($fakeTemplatesDetails, $templatesDetails->id);
        $this->assertModelData($fakeTemplatesDetails, $updatedTemplatesDetails->toArray());
        $dbTemplatesDetails = $this->templatesDetailsRepo->find($templatesDetails->id);
        $this->assertModelData($fakeTemplatesDetails, $dbTemplatesDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTemplatesDetails()
    {
        $templatesDetails = $this->makeTemplatesDetails();
        $resp = $this->templatesDetailsRepo->delete($templatesDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(TemplatesDetails::find($templatesDetails->id), 'TemplatesDetails should not exist in DB');
    }
}
