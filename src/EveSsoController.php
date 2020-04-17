<?php
/**
 * Controller for EVESSO Socialite driver
 */
namespace Arcyfa\EveSso;

use Socialite;
use Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class EveSsoController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * Somehow this those not work
     *
     * @var string
     */
    protected $redirectTo = '/success';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider( $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from CCP
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        // $user = Socialite::driver($provider)->user();
        // $user->token;
        $userSocial = Socialite::driver($provider)->stateless()->user();

        //dd($userSocial);

        $user = User::where(['provider_id' => $userSocial->getId()])->first();
        if($user){
            Auth::login($user);
            return redirect()->route('success'); // never executed due to Auth::login
        }else{
            $user = User::create([
                'name'          => $userSocial->getName(),
                'email'         => $userSocial->getEmail(),
                'provider'      => $provider,
                'provider_id'   => $userSocial->getId(),
                'avatar'        => $userSocial->avatar,
                'token'         => $userSocial->token,
                'refresh_token' => $userSocial->refreshToken,
                'token_type'    => $userSocial->user['TokenType'],
                'expires_in'    => $userSocial->expiresIn,
                'expires_on'    => $userSocial->user['ExpiresOn'],
                'character_owner_hash' => $userSocial->user['CharacterOwnerHash']
            ]);
            //dd($provider,$userSocial, $user);
            $user->save(); // I believe create saves user aswell
            Auth::login($user);
            return redirect()->route('new_user'); // never executed due to Auth::login
        }
    }

    /*
        I think this is not used
        Place holder for once it finaly works
    */
    public function success(User $user){
        echo "
            <h1>Welcome back ". $user->name ."
            <img src='". $user->avatar ."'>
            <p>Thats it bye bye!</p>
        ";
    }

    public function new_user(User $user){
        echo "
            <h1>Register ". $user->name ."
            <img src='". $user->avatar ."'>
            <p>Thats it bye bye!</p>
        ";
    }
    /**/
}
