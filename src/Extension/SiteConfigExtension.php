<?php

namespace Syntro\SilverstripeKlaro\Extension;

use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\CMS\Model\SiteTree;

/**
 * Extends the siteconfig with fields for the Messages displayed in the klaro
 * dialog
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 * @codeCoverageIgnore
 */
class SiteConfigExtension extends DataExtension
{
    /**
     * Database fields
     * @config
     * @var array
     */
    private static $db = [
        'ConsentNotice' => 'Text',
        'ConsentModal' => 'Text'
    ];

    /**
     * Has_one relationship
     * @config
     * @var array
     */
    private static $has_one = [
        'PrivacyPolicyPage' => SiteTree::class,
    ];

    /**
     * Defines db fields that are translatable.
     * @config
     * @var array
     */
    private static $translate = [
        'ConsentNotice',
        'ConsentModal'
    ];

    /**
     * Update Fields
     * @param FieldList $fields the original fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        $fields->findOrMakeTab(
            "Root.CookieNotice",
            $owner->fieldLabel('Root.CookieNotice')
        );
        $fields->addFieldsToTab(
            'Root.CookieNotice',
            [
                $noticeField = TextareaField::create(
                    'ConsentNotice',
                    $owner->fieldLabel('ConsentNotice')
                ),
                $modalField = TextareaField::create(
                    'ConsentModal',
                    $owner->fieldLabel('ConsentModal')
                ),
                $policyField = TreeDropdownField::create(
                    'PrivacyPolicyPageID',
                    $owner->fieldLabel('PrivacyPolicyPage'),
                    SiteTree::class
                )
            ]
        );
        $noticeField->setRightTitle(_t(__CLASS__ . '.CONSENTNOTICEDESC', 'This text will replace the default text in the initial notice.'));
        $modalField->setRightTitle(_t(__CLASS__ . '.CONSENTMODALDESC', 'This text will replace the default text in the selection modal.'));
        $policyField->setRightTitle(_t(__CLASS__ . '.PRIVACYPOLICYPAGEDESC', 'If set, a link to this Page is displayed in the selection modal.'));
        return $fields;
    }


    /**
     * updateFieldLabels
     *
     * @param  array $labels the original labels
     * @return array
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['ConsentNotice'] = _t(__CLASS__ . '.CONSENTNOTICELABEL', 'Consent Notice Text');
        $labels['ConsentModal'] = _t(__CLASS__ . '.CONSENTMODALLABEL', 'Consent Modal Text');
        $labels['PrivacyPolicyPage'] = _t(__CLASS__ . '.PRIVACYPOLICYPAGELABEL', 'Privacy Policy');
        $labels['Root.CookieNotice'] = _t(__CLASS__ . '.COOKIENOTICETAB', 'Cookie Notice');
        return $labels;
    }
}
