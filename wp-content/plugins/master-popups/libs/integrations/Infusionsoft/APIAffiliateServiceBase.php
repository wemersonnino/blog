<?php
class Mpp_Infusionsoft_APIAffiliateServiceBase extends Mpp_Infusionsoft_Service{

    public static function affPayouts($affiliateId, $filterStartDate, $filterEndDate, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $affiliateId,
            $filterStartDate,
            $filterEndDate
        );

        return parent::send($app, "APIAffiliateService.affPayouts", $params);
    }

    public static function affCommissions($affiliateId, $filterStartDate, $filterEndDate, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $affiliateId,
            $filterStartDate,
            $filterEndDate
        );

        return parent::send($app, "APIAffiliateService.affCommissions", $params, null, true);
    }

    public static function affClawbacks($affiliateId, $filterStartDate, $filterEndDate, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $affiliateId,
            $filterStartDate,
            $filterEndDate
        );

        return parent::send($app, "APIAffiliateService.affClawbacks", $params);
    }

    public static function affSummary($affiliateIds, $filterStartDate, $filterEndDate, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $affiliateIds,
            $filterStartDate,
            $filterEndDate
        );

        return parent::send($app, "APIAffiliateService.affSummary", $params);
    }

    public static function affRunningTotals($affiliateIds, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $affiliateIds
        );

        return parent::send($app, "APIAffiliateService.affRunningTotals", $params);
    }

    public static function updatePhoneStats($firstName, $lastName, $calls, $totalTime, $averageTime, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $firstName,
            $lastName,
            (int) $calls,
            $totalTime,
            $averageTime
        );

        return parent::send($app, "AffiliateService.updatePhoneStats", $params);
    }

    public static function getRedirectLinksForAffiliate($affiliateId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $affiliateId
        );

        return parent::send($app, "AffiliateService.getRedirectLinksForAffiliate", $params);
    }



}