<?php

namespace Lezhnev74\LaravelSocialiteWix;

use SocialiteProviders\Manager\SocialiteWasCalled;

class WixExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('wix', Provider::class);
    }
}