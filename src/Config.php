<?php

namespace Syntro\SilverstripeKlaro;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\View\SSViewer;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * allows the configuration of klaro using the SilverStripe yaml config interface
 *
 * The configuration follows https://kiprotect.com/docs/klaro/annotated-configuration
 * as far as possible. Language is handled by outputting only one key which
 * corresponds to the current i18n lang.
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class Config
{
    use Configurable;

    /**
     * Array specifying additional global options. The options are described here:
     * https://kiprotect.com/docs/klaro/annotated-configuration
     * valid keys are:
     * - 'testing'
     * - 'elementID'
     * - 'storageMethod'
     * - 'storageName'
     * - 'htmlTexts' (default: true)
     * - 'cookieDomain'
     * - 'cookieExpiresAfterDays'
     * - 'default'
     * - 'mustConsent'
     * - 'acceptAll'
     * - 'hideDeclineAll'
     * - 'hideLearnMore'
     *
     * @config
     * @var array
     */
    private static $klaro_options = [
        'htmlTexts' => true
    ];

     /**
      * Contains purposes used by klaro. This is an array where the keys
      * specify the  identifier of the purpose and value is an array with:
      * - 'title' : the title of the purpose
      *
      * @config
      * @var array
      */
    private static $klaro_purposes = [
         'required' => ['title' => 'Required Services']
     ];

     /**
      * Contains the services managed by klaro. This is an array where the key
      * represents the identifier (name used in KlaroRequirements) of the service.
      * The value is an array containing the config for the service. Allowed
      * options:
      * - 'title'* : (string) the title of the service
      * - 'purposes'* : (string[]) purposes this service is listed under
      * - 'description' : (string) the description of the service
      * - 'default' : (boolean) specifying the state in the selection modal
      * - 'cookies' : (string[]|array[]) cookies used by the service, see https://kiprotect.com/docs/klaro/annotated-configuration
      * - 'required' : (boolean) specify if this service is required
      * - 'optOut' : (boolean) make this service opt out
      * - 'onlyOnce' : (boolean) load this service only once
      *
      * @config
      * @var array
      */
    private static $klaro_services = [
         'session' => [
             'title' => 'Session default',
             'purposes' => ['required'],
             'cookies' => [
                 '/^PHP.*$/',
                 ['/^PHP.*$/', '/', 'localhost']
             ],
             'default' => true,
             'required' => true
         ]
     ];

     /**
      * getLang - returns the language part from the current locale
      *
      * @return string
      */
    public static function getLang()
    {
        return i18n::getData()->langFromLocale(i18n::get_locale());
    }

    /**
     * render - renders the config as a js script
     *
     * @return string
     */
    public static function render()
    {
        $config = [
            'translations' => self::getTranslationsForTemplate(),
            'services' => self::getServicesForTemplate()
        ];
        foreach (self::getOptions() as $option => $value) {
            if ($option == 'translations' || $option == 'services') {
                throw new \InvalidArgumentException(
                    sprintf('the option "%s" must not be set via $klaro_options', $option),
                    1
                );
            }
            $config[$option] = $value;
        }
        $config = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        return SSViewer::execute_template('Syntro/SilverstripeKlaro/Config', ArrayData::create([
            'KlaroConfig' => DBHTMLText::create()->setValue($config),
        ]));
    }

    /**
     * getPurposes - returns the configured purposes
     *
     * @return array
     */
    public static function getPurposes()
    {
        return \SilverStripe\Core\Config\Config::inst()->get(self::class, 'klaro_purposes');
    }

    /**
     * getServices - returns the configured services
     *
     * @return array
     */
    public static function getServices()
    {
        return \SilverStripe\Core\Config\Config::inst()->get(self::class, 'klaro_services');
    }

    /**
     * getOptions - returns the configured services
     *
     * @return array
     */
    public static function getOptions()
    {
        return \SilverStripe\Core\Config\Config::inst()->get(self::class, 'klaro_options');
    }

    /**
     * getTranslationsForTemplate - constructs the translation key
     *
     * @return array
     */
    public static function getTranslationsForTemplate()
    {
        $translations = [
            'purposes' => self::getPurposesForTemplate()
        ];
        return [self::getLang() => $translations];
    }

    /**
     * getPurposesForTemplate - return templatable descriptions of the purposes
     *
     * @return array
     */
    private static function getPurposesForTemplate()
    {
        $configuredPurposes = self::getPurposes();
        $purposes = [];
        foreach ($configuredPurposes as $name => $data) {
            if (!isset($data['title'])) {
                throw new \InvalidArgumentException(
                    sprintf('the "title" field for the purpose with key "%s" is not set!', $name),
                    1
                );
            }
            $purposes[$name] = _t(__CLASS__ . '.purpose_' . $name . '_title', $data['title']);
        }
        return $purposes;
    }


    /**
     * getServiceForTemplate - return templatable descriptions of the services
     *
     * @return array
     */
    private static function getServicesForTemplate()
    {
        $configuredServices = self::getServices();
        $services = [];
        foreach ($configuredServices as $name => $data) {
            // We first do some checks to validate the service definition
            // 1) ensure a title is set (otherwise the default translation is
            // missing)
            if (!isset($data['title'])) {
                throw new \InvalidArgumentException(
                    sprintf('the "title" field for the service with key "%s" is not set!', $name),
                    1
                );
            }
            // 2) ensure the service has a valid purpose field (klaro errors if
            // a service does not specify a purpose)
            if (!isset($data['purposes']) || empty($data['purposes'])) {
                throw new \InvalidArgumentException(
                    sprintf('service "%s" does not contain a valid set of purposes! (found %s)', $name, json_encode($data)),
                    1
                );
            }

            $service = [
                'name' => $name,
                'translations' => [
                    self::getLang() => [
                        'title' => _t(__CLASS__ . '.service_' . $name . '_title', $data['title']),
                    ]
                ],
                'purposes' => $data['purposes'],
            ];
            if (isset($data['description'])) {
                $service['translations'][self::getLang()]['description'] = _t(__CLASS__ . '.service_' . $name . '_description', $data['description']);
            }
            $extraKeys = [
                'cookies',
                'default',
                'required',
                'optOut',
                'onlyOnce',
            ];
            foreach ($extraKeys as $key) {
                if (isset($data[$key])) {
                    $service[$key] = $data[$key];
                }
            }
            $services[] = $service;
        }
        return $services;
    }
}
