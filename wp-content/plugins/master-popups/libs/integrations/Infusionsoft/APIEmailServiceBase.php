<?php
class Mpp_Infusionsoft_APIEmailServiceBase extends Mpp_Infusionsoft_Service{

    public static function attachEmail($contactId, $fromName, $fromAddress, $toAddress, $ccAddresses, $bccAddresses, $contentType, $subject, $htmlBody, $textBody, $header, $receivedDate, $sentDate, $emailSentType, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            $fromName,
            $fromAddress,
            $toAddress,
            $ccAddresses,
            $bccAddresses,
            $contentType,
            $subject,
            $htmlBody,
            $textBody,
            $header,
            $receivedDate,
            $sentDate,
            (int) $emailSentType
        );

        return parent::send($app, "APIEmailService.attachEmail", $params);
    }

    public static function sendEmail($contactList, $fromAddress, $toAddress, $ccAddresses, $bccAddresses, $contentType, $subject, $htmlBody, $textBody, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $contactList,
            $fromAddress,
            $toAddress,
            $ccAddresses,
            $bccAddresses,
            $contentType,
            $subject,
            $htmlBody,
            $textBody
        );

        return parent::send($app, "APIEmailService.sendEmail", $params);
    }

    public static function sendTemplate($contactList, $templateId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $contactList,
            (int) $templateId,
        );

        return parent::send($app, "APIEmailService.sendEmail", $params);
    }
    public static function createEmailTemplate($templateTitle, $visibility, $fromAddress, $toAddress, $ccAddresses, $bccAddresses, $contentType, $subject, $htmlBody, $textBody, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $templateTitle,
            (int) $visibility,
            $fromAddress,
            $toAddress,
            $ccAddresses,
            $bccAddresses,
            $contentType,
            $subject,
            $htmlBody,
            $textBody
        );

        return parent::send($app, "APIEmailService.createEmailTemplate", $params);
    }

    public static function addEmailTemplate($pieceTitle, $categories, $fromAddress, $toAddress, $ccAddress, $bccAddress, $subject, $textBody, $htmlBody, $contentType, $mergeContext, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $pieceTitle,
            $categories,
            $fromAddress,
            $toAddress,
            $ccAddress,
            $bccAddress,
            $subject,
            $textBody,
            $htmlBody,
            $contentType,
            $mergeContext
        );

        return parent::send($app, "APIEmailService.addEmailTemplate", $params);
    }

    public static function updateEmailTemplate($templateId, $pieceTitle, $categories, $fromAddress, $toAddress, $ccAddress, $bccAddress, $subject, $textBody, $htmlBody, $contentType, $mergeContext, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $templateId,
            $pieceTitle,
            $categories,
            $fromAddress,
            $toAddress,
            $ccAddress,
            $bccAddress,
            $subject,
            $textBody,
            $htmlBody,
            $contentType,
            $mergeContext
        );

        return parent::send($app, "APIEmailService.updateEmailTemplate", $params, null, true);
    }

    public static function getEmailTemplate($templateId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $templateId
        );

        return parent::send($app, "APIEmailService.getEmailTemplate", $params, null, true);
    }

    public static function getAvailableMergeFields($mergeContext, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $mergeContext
        );

        return parent::send($app, "APIEmailService.getAvailableMergeFields", $params, null, true);
    }

    public static function optIn($email, $permissionReason, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $email,
            $permissionReason
        );

        return parent::send($app, "APIEmailService.optIn", $params, null, true);
    }

    public static function optOut($email, $reason, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $email,
            $reason
        );

        return parent::send($app, "APIEmailService.optOut", $params, null, true);
    }

    public static function getOptStatus($email, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $email
        );

        return parent::send($app, "APIEmailService.getOptStatus", $params, null, true);
    }

}