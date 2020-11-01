<?php

namespace Syntro\SilverstripeKlaro\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use SilverStripe\View\HTML;
use Syntro\SilverstripeKlaro\KlaroRequirements;

use Syntro\SilverstripeKlaro\Config;

/**
 * Extends the default content controller to include the built klaro.js
 * script and css
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 * @codeCoverageIgnore
 */
class ContentControllerExtension extends Extension
{

    /**
     * onBeforeInit - Handler executed before init
     *
     * @return void
     */
    public function onBeforeInit()
    {
        Requirements::css('syntro/silverstripe-klaro:client/dist/bundle.css');
        Requirements::insertHeadTags(HTML::createTag(
            'script',
            [
                'type' => 'application/javascript'
            ],
            "//<![CDATA[\n" . Config::render() . "\n//]]>"
        ));
        Requirements::javascript('syntro/silverstripe-klaro:client/dist/klaro.js', ['defer' => true]);
    }
}
