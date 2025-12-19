<?php

use App\Models\YesNoSelection;
use App\Repositories\YesNoSelectionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YesNoSelectionRepositoryTest extends TestCase
{
    use MakeYesNoSelectionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var YesNoSelectionRepository
     */
    protected $yesNoSelectionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->yesNoSelectionRepo = App::make(YesNoSelectionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateYesNoSelection()
    {
        $yesNoSelection = $this->fakeYesNoSelectionData();
        $createdYesNoSelection = $this->yesNoSelectionRepo->create($yesNoSelection);
        $createdYesNoSelection = $createdYesNoSelection->toArray();
        $this->assertArrayHasKey('id', $createdYesNoSelection);
        $this->assertNotNull($createdYesNoSelection['id'], 'Created YesNoSelection must have id specified');
        $this->assertNotNull(YesNoSelection::find($createdYesNoSelection['id']), 'YesNoSelection with given id must be in DB');
        $this->assertModelData($yesNoSelection, $createdYesNoSelection);
    }

    /**
     * @test read
     */
    public function testReadYesNoSelection()
    {
        $yesNoSelection = $this->makeYesNoSelection();
        $dbYesNoSelection = $this->yesNoSelectionRepo->find($yesNoSelection->id);
        $dbYesNoSelection = $dbYesNoSelection->toArray();
        $this->assertModelData($yesNoSelection->toArray(), $dbYesNoSelection);
    }

    /**
     * @test update
     */
    public function testUpdateYesNoSelection()
    {
        $yesNoSelection = $this->makeYesNoSelection();
        $fakeYesNoSelection = $this->fakeYesNoSelectionData();
        $updatedYesNoSelection = $this->yesNoSelectionRepo->update($fakeYesNoSelection, $yesNoSelection->id);
        $this->assertModelData($fakeYesNoSelection, $updatedYesNoSelection->toArray());
        $dbYesNoSelection = $this->yesNoSelectionRepo->find($yesNoSelection->id);
        $this->assertModelData($fakeYesNoSelection, $dbYesNoSelection->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteYesNoSelection()
    {
        $yesNoSelection = $this->makeYesNoSelection();
        $resp = $this->yesNoSelectionRepo->delete($yesNoSelection->id);
        $this->assertTrue($resp);
        $this->assertNull(YesNoSelection::find($yesNoSelection->id), 'YesNoSelection should not exist in DB');
    }
}
