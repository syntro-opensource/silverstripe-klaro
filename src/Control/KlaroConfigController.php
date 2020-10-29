<?php

namespace Syntro\SilverstripeKlaro\Extension;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * generates an endpoint from which to load the config for klaro, which is
 * populated with all defined Scripts
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class KlaroConfigController extends Controller
{
    /**
     * index - description
     *
     * @param  HTTPRequest $request the http request
     * @return string|HTTPResponse
     */
    public function index(HTTPRequest $request)
    {
        $this->getResponse()->addHeader("Content-Type", "text/javascript; charset=utf-8");
        return $this->renderWith('Syntro/SilverstripeKlaro/KlaroConfigController');
    }
}
