<?php namespace Tests\Repositories;

use App\Models\YesNoMinusSelectionLanguage;
use App\Repositories\YesNoMinusSelectionLanguageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class YesNoMinusSelectionLanguageRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var YesNoMinusSelectionLanguageRepository
     */
    protected $yesNoMinusSelectionLanguageRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->yesNoMinusSelectionLanguageRepo = \App::make(YesNoMinusSelectionLanguageRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->make()->toArray();

        $createdYesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepo->create($yesNoMinusSelectionLanguage);

        $createdYesNoMinusSelectionLanguage = $createdYesNoMinusSelectionLanguage->toArray();
        $this->assertArrayHasKey('id', $createdYesNoMinusSelectionLanguage);
        $this->assertNotNull($createdYesNoMinusSelectionLanguage['id'], 'Created YesNoMinusSelectionLanguage must have id specified');
        $this->assertNotNull(YesNoMinusSelectionLanguage::find($createdYesNoMinusSelectionLanguage['id']), 'YesNoMinusSelectionLanguage with given id must be in DB');
        $this->assertModelData($yesNoMinusSelectionLanguage, $createdYesNoMinusSelectionLanguage);
    }

    /**
     * @test read
     */
    public function test_read_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->create();

        $dbYesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepo->find($yesNoMinusSelectionLanguage->id);

        $dbYesNoMinusSelectionLanguage = $dbYesNoMinusSelectionLanguage->toArray();
        $this->assertModelData($yesNoMinusSelectionLanguage->toArray(), $dbYesNoMinusSelectionLanguage);
    }

    /**
     * @test update
     */
    public function test_update_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->create();
        $fakeYesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->make()->toArray();

        $updatedYesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepo->update($fakeYesNoMinusSelectionLanguage, $yesNoMinusSelectionLanguage->id);

        $this->assertModelData($fakeYesNoMinusSelectionLanguage, $updatedYesNoMinusSelectionLanguage->toArray());
        $dbYesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepo->find($yesNoMinusSelectionLanguage->id);
        $this->assertModelData($fakeYesNoMinusSelectionLanguage, $dbYesNoMinusSelectionLanguage->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_yes_no_minus_selection_language()
    {
        $yesNoMinusSelectionLanguage = factory(YesNoMinusSelectionLanguage::class)->create();

        $resp = $this->yesNoMinusSelectionLanguageRepo->delete($yesNoMinusSelectionLanguage->id);

        $this->assertTrue($resp);
        $this->assertNull(YesNoMinusSelectionLanguage::find($yesNoMinusSelectionLanguage->id), 'YesNoMinusSelectionLanguage should not exist in DB');
    }
}
