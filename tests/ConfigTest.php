<?php

namespace Syntro\SilverstripeKlaro\Tests;

use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Dev\SapphireTest;
use Syntro\SilverstripeKlaro\Config;
use \SilverStripe\Core\Config\Config as SilverStripeConfig;
use SilverStripe\i18n\i18n;

/**
 * Test the Config generation
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ConfigTest extends SapphireTest
{
    protected static $fixture_file = './fixture.yml';

    /**
     * assert that the rendering fails when a purpose has no title
     *
     * @return void
     */
    public function testFailWithoutPurposeTitle()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'emptyPurpose' => []
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('the "title" field for the purpose with key "emptyPurpose" is not set');
        Config::render();
    }

    /**
     * assert that a render fails if a service does not contain a valid title
     *
     * @return void
     */
    public function testFailWithoutServiceTitle()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['purposes' => ['purpose']]
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('the "title" field for the service with key "service1" is not set');
        Config::render();
    }

    /**
     * assert that a render fails if a service does not contain a valid set of
     * purposes.
     *
     * @return void
     */
    public function testFailWithoutServicePurpose()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['title' => 'service1']
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('service "service1" does not contain a valid set of purposes');
        Config::render();
    }

    /**
     * assert that a render fails if a service does not contain any purposes.
     *
     * @return void
     */
    public function testFailWithEmptyServicePurpose()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['title' => 'service1', 'purposes' => []]
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('service "service1" does not contain a valid set of purposes');
        Config::render();
    }

    /**
     * testPurposesForTemplate
     *
     * @return void
     */
    public function testPurposesForTemplate()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        i18n::set_locale('de_CH');
        $purposes = Config::getPurposesForTemplate();
        $this->assertEquals(1, count($purposes));
        $this->assertArrayHasKey('purpose', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose']);
        $this->assertArrayNotHasKey('description', $purposes['purpose']);
        $this->assertEquals('testtitle', $purposes['purpose']['title']);

        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => [
                'title' => 'testtitle2',
                'description' => 'testdescription'
            ]
        ]);
        $purposes = Config::getPurposesForTemplate();
        $this->assertEquals(1, count($purposes));
        $this->assertArrayHasKey('purpose', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose']);
        $this->assertEquals('testtitle2', $purposes['purpose']['title']);
        $this->assertArrayHasKey('description', $purposes['purpose']);
        $this->assertEquals('testdescription', $purposes['purpose']['description']);

        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose1' => [
                'title' => 'testtitle2',
                'description' => 'testdescription'
            ],
            'purpose2' => [
                'title' => 'testtitle2'
            ]
        ]);
        $purposes = Config::getPurposesForTemplate();
        $this->assertEquals(2, count($purposes));
        $this->assertArrayHasKey('purpose1', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose1']);
        $this->assertArrayHasKey('description', $purposes['purpose1']);

        $this->assertArrayHasKey('purpose2', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose2']);
        $this->assertArrayNotHasKey('description', $purposes['purpose2']);
    }

    /**
     * testServicesForTemplate
     *
     * @return void
     */
    public function testServicesForTemplatePopulatesService()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['title' => 'service1', 'purposes' => ['purpose']]
        ]);
        i18n::set_locale('de_CH');

        $services = Config::getServicesForTemplate();
        $this->assertEquals(1, count($services));
        $service = $services[0];
        $this->assertArrayHasKey('translations', $service);
        $this->assertArrayHasKey('de', $service['translations']);
        $this->assertArrayHasKey('title', $service['translations']['de']);
        $this->assertArrayNotHasKey('description', $service['translations']['de']);
        $this->assertArrayHasKey('name', $service);
        $this->assertEquals('service1', $service['name']);
        $this->assertArrayHasKey('purposes', $service);
        $this->assertEquals(['purpose'], $service['purposes']);
        $this->assertArrayNotHasKey('cookies', $service);
        $this->assertArrayNotHasKey('default', $service);
        $this->assertArrayNotHasKey('required', $service);
        $this->assertArrayNotHasKey('optOut', $service);
        $this->assertArrayNotHasKey('onlyOnce', $service);


        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => [
                'title' => 'service1',
                'description' => 'description of this service',
                'cookies' => ['cookie1', 'cookie2'],
                'default' => true,
                'required' => true,
                'optOut' => true,
                'onlyOnce' => true,
                'purposes' => ['purpose']
            ]
        ]);
        $services = Config::getServicesForTemplate();
        $this->assertEquals(1, count($services));
        $service = $services[0];
        $this->assertArrayHasKey('translations', $service);
        $this->assertArrayHasKey('de', $service['translations']);
        $this->assertEquals('service1', $service['translations']['de']['title']);
        $this->assertEquals('description of this service', $service['translations']['de']['description']);
        $this->assertEquals(['purpose'], $service['purposes']);
        $this->assertEquals(['cookie1', 'cookie2'], $service['cookies']);
        $this->assertEquals(true, $service['default']);
        $this->assertEquals(true, $service['required']);
        $this->assertEquals(true, $service['optOut']);
        $this->assertEquals(true, $service['onlyOnce']);
    }

    /**
     * testTranslationsForTemplate
     *
     * @return void
     */
    public function testTranslationsForTemplate()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['title' => 'service1', 'purposes' => ['purpose']]
        ]);
        i18n::set_locale('de_CH');

        $translations = Config::getTranslationsForTemplate();
        $this->assertArrayHasKey('de', $translations);
        $this->assertArrayHasKey('purposes', $translations['de']);
        $this->assertArrayHasKey('purpose', $translations['de']['purposes']);
    }

    /**
     * testSiteconfigFields
     *
     * @return void
     */
    public function testSiteconfigFields()
    {
        i18n::set_locale('de_CH');
        $config = SiteConfig::current_site_config();

        $translations = Config::getTranslationsForTemplate();
        $this->assertArrayNotHasKey('consentNotice', $translations['de']);
        $this->assertArrayNotHasKey('consentModal', $translations['de']);

        $config->ConsentNotice = 'Notice';
        $config->ConsentModal = 'Modal';
        $config->write();

        $translations = Config::getTranslationsForTemplate();
        $this->assertArrayHasKey('consentNotice', $translations['de']);
        $this->assertEquals('Notice', $translations['de']['consentNotice']['description']);
        $this->assertArrayHasKey('consentModal', $translations['de']);
        $this->assertEquals('Modal', $translations['de']['consentModal']['description']);
    }
}
