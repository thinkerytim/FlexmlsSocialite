<?php

namespace SocialiteProviders\Flexmls;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'FLEXMLS';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://sparkplatform.com/oauth2', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://sparkapi.com/v1/oauth2/grant';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://sparkapi.com/v1/my/account', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'User-Agent' => 'Socialite',
                'X-SparkApi-User-Agent' => 'Socialite',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['username'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => $user['avatar'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }
}
