<?php
namespace App\Services\hrms\feature;

use App\Models\HRFeatureFlags;

class FeatureFlagService{
    public static function isFeatureEnabled($featureName){

        $result = HRFeatureFlags::where('feature_name', $featureName)
            ->where('is_enabled', 1)
            ->value('feature_name');
        return empty($result) ? 0 : 1;
    }
}
