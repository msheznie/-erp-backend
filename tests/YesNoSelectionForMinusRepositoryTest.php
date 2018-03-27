<?php

use App\Models\YesNoSelectionForMinus;
use App\Repositories\YesNoSelectionForMinusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YesNoSelectionForMinusRepositoryTest extends TestCase
{
    use MakeYesNoSelectionForMinusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var YesNoSelectionForMinusRepository
     */
    protected $yesNoSelectionForMinusRepo;

    public function setUp()
    {
        parent::setUp();
        $this->yesNoSelectionForMinusRepo = App::make(YesNoSelectionForMinusRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->fakeYesNoSelectionForMinusData();
        $createdYesNoSelectionForMinus = $this->yesNoSelectionForMinusRepo->create($yesNoSelectionForMinus);
        $createdYesNoSelectionForMinus = $createdYesNoSelectionForMinus->toArray();
        $this->assertArrayHasKey('id', $createdYesNoSelectionForMinus);
        $this->assertNotNull($createdYesNoSelectionForMinus['id'], 'Created YesNoSelectionForMinus must have id specified');
        $this->assertNotNull(YesNoSelectionForMinus::find($createdYesNoSelectionForMinus['id']), 'YesNoSelectionForMinus with given id must be in DB');
        $this->assertModelData($yesNoSelectionForMinus, $createdYesNoSelectionForMinus);
    }

    /**
     * @test read
     */
    public function testReadYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->makeYesNoSelectionForMinus();
        $dbYesNoSelectionForMinus = $this->yesNoSelectionForMinusRepo->find($yesNoSelectionForMinus->id);
        $dbYesNoSelectionForMinus = $dbYesNoSelectionForMinus->toArray();
        $this->assertModelData($yesNoSelectionForMinus->toArray(), $dbYesNoSelectionForMinus);
    }

    /**
     * @test update
     */
    public function testUpdateYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->makeYesNoSelectionForMinus();
        $fakeYesNoSelectionForMinus = $this->fakeYesNoSelectionForMinusData();
        $updatedYesNoSelectionForMinus = $this->yesNoSelectionForMinusRepo->update($fakeYesNoSelectionForMinus, $yesNoSelectionForMinus->id);
        $this->assertModelData($fakeYesNoSelectionForMinus, $updatedYesNoSelectionForMinus->toArray());
        $dbYesNoSelectionForMinus = $this->yesNoSelectionForMinusRepo->find($yesNoSelectionForMinus->id);
        $this->assertModelData($fakeYesNoSelectionForMinus, $dbYesNoSelectionForMinus->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->makeYesNoSelectionForMinus();
        $resp = $this->yesNoSelectionForMinusRepo->delete($yesNoSelectionForMinus->id);
        $this->assertTrue($resp);
        $this->assertNull(YesNoSelectionForMinus::find($yesNoSelectionForMinus->id), 'YesNoSelectionForMinus should not exist in DB');
    }
}
