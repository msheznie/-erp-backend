<?php

use Faker\Factory as Faker;
use App\Models\AuditTrail;
use App\Repositories\AuditTrailRepository;

trait MakeAuditTrailTrait
{
    /**
     * Create fake instance of AuditTrail and save it in database
     *
     * @param array $auditTrailFields
     * @return AuditTrail
     */
    public function makeAuditTrail($auditTrailFields = [])
    {
        /** @var AuditTrailRepository $auditTrailRepo */
        $auditTrailRepo = App::make(AuditTrailRepository::class);
        $theme = $this->fakeAuditTrailData($auditTrailFields);
        return $auditTrailRepo->create($theme);
    }

    /**
     * Get fake instance of AuditTrail
     *
     * @param array $auditTrailFields
     * @return AuditTrail
     */
    public function fakeAuditTrail($auditTrailFields = [])
    {
        return new AuditTrail($this->fakeAuditTrailData($auditTrailFields));
    }

    /**
     * Get fake data of AuditTrail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAuditTrailData($auditTrailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'valueFrom' => $fake->randomDigitNotNull,
            'valueTo' => $fake->randomDigitNotNull,
            'valueFromSystemID' => $fake->randomDigitNotNull,
            'valueFromText' => $fake->word,
            'valueToSystemID' => $fake->randomDigitNotNull,
            'valueToText' => $fake->word,
            'description' => $fake->text,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $auditTrailFields);
    }
}
