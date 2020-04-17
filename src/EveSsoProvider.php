<?php
/***
 * Driver for Socialite allowing EVE Online SSO authentication to be included
 */
namespace Arcyfa\EveSso;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;
use GuzzleHttp\ClientInterface;

class EveSsoProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The separating character for the requested scopes.
     * Eve requires a space separated list of scopes
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     * A full list off scopes will be available in config/esi.php
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        // fetch scopes from saperate config
        // Custom set of scopes could be set now before request is made
        $this->setScopes( config('eve-sso.scopes') );
        return $this->buildAuthUrlFromBase('https://login.eveonline.com/v2/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://login.eveonline.com/v2/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {

        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        // EVE requires us to build a authorization code
        $authorizationCode =
            base64_encode($this->clientId.":".$this->clientSecret);

        // Using default html header authorization
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic '. $authorizationCode,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Host' =>  'login.eveonline.com'
            ],
            $postKey => $this->getTokenFields($code),
        ]);
        //dd(json_decode($response->getBody(), true)); // works
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {

        $response = $this->getHttpClient()->get('https://login.eveonline.com/oauth/verify', [
            'query' => [
                'prettyPrint' => 'false',
            ],
            'headers' => [
                'User-Agent' => 'Eve-Web/0.0.1 (+) Arcyfa-EveSso/0.0.1 Socialite/4.1 Laravel/5.8',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);
        //Linux 47-Ubuntu x86_64 GNU/Linux
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => Arr::get($user, 'CharacterID'),
            'nickname' => Arr::get($user, 'CharacterName'),
            'name' => Arr::get($user, 'CharacterName'),
            'email' => null,
            "avatar" => "https://imageserver.eveonline.com/Character/". Arr::get($user, 'CharacterID') ."_128.jpg",
        ]);
    }
}
