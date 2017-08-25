<?php

namespace sws\protocols;

/**
 * This is the version of websockets used by Chrome versions 17 through 19.
 *
 * @see http://tools.ietf.org/html/rfc6455
 */
class Rfc6455Protocol extends Protocol
{
    const VERSION = 13;

    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * This is our most recent protocol class
     */
    public function acceptsVersion($version)
    {
        if ((int)$version <= 13) {
            return true;
        }
        return false;
    }
}
