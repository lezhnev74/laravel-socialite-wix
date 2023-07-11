<?php

namespace Lezhnev74\LaravelSocialiteWix;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'WIX';

    /**
     * @see https://dev.wix.com/api/rest/getting-started/authentication#getting-started_authentication_step-2-app-sends-users-to-authorize-the-app
     */
    protected function getCodeFields($state = null)
    {
        $fields = [
            'appId' => $this->clientId,
            'redirectUrl' => $this->redirectUrl,
        ];
        if ($this->request->get('token')) {
            $fields['token'] = $this->request->get('token'); // to support App Installation from Marketplace
        }

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return array_merge($fields, $this->parameters);
    }

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.wix.com/installer/install', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://www.wixapis.com/oauth/access';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://www.wixapis.com/apps/v1/instance',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => $token,
                ],
            ]
        );

        // @see https://dev.wix.com/api/rest/app-management/apps/app-instance/app-instance-object
        return json_decode((string)$response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['instance']['instanceId'],
            'email' => $user['site']['ownerEmail'],
            'name' => $user['site']['siteDisplayName'],
        ]);
    }

}