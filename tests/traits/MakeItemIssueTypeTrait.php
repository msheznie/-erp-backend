<?php

use Faker\Factory as Faker;
use App\Models\ItemIssueType;
use App\Repositories\ItemIssueTypeRepository;

trait MakeItemIssueTypeTrait
{
    /**
     * Create fake instance of ItemIssueType and save it in database
     *
     * @param array $itemIssueTypeFields
     * @return ItemIssueType
     */
    public function makeItemIssueType($itemIssueTypeFields = [])
    {
        /** @var ItemIssueTypeRepository $itemIssueTypeRepo */
        $itemIssueTypeRepo = App::make(ItemIssueTypeRepository::class);
        $theme = $this->fakeItemIssueTypeData($itemIssueTypeFields);
        return $itemIssueTypeRepo->create($theme);
    }

    /**
     * Get fake instance of ItemIssueType
     *
     * @param array $itemIssueTypeFields
     * @return ItemIssueType
     */
    public function fakeItemIssueType($itemIssueTypeFields = [])
    {
        return new ItemIssueType($this->fakeItemIssueTypeData($itemIssueTypeFields));
    }

    /**
     * Get fake data of ItemIssueType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemIssueTypeData($itemIssueTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'issueTypeDes' => $fake->word
        ], $itemIssueTypeFields);
    }
}
