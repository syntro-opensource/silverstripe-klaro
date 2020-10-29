<?php

namespace Syntro\SilverstripeKlaro\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

use Syntro\SilverstripeKlaro\KlaroRequirements;

/**
 * Extends the default content controller to include the built klaro.js
 * script and css
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
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
        Requirements::javascript('/_klaro-config.js', ['defer' => true]);
        Requirements::javascript('syntro/silverstripe-klaro:client/dist/klaro.js', ['defer' => true]);

        KlaroRequirements::customKlaroScript(
            <<<JS
          alert("hi there");
        JS,
            'ganalytics'
        );
    }
}
