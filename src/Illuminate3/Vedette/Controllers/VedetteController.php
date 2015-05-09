<?php namespace Illuminate3\Vedette\Controllers;

//
// @author Steve Montambeault
// @link   http://stevemo.ca
//

use View;
use Config;
use Input;
use Sentry;
use Redirect;
use Lang;
use Event;
use Validator;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;
//use Cartalyst\Sentry\Users\UserInterface;
use URL;
use Mail;
use Session;

class VedetteController extends BaseController {

	public function index()
	{
		return View::make(Config::get('vedette::vedette_views.auth'));
	}

	/**
	 * Account sign in.
	 *
	 * @return View
	 */
	public function getLogin()
	{
		// Is the user logged in?
		if (Sentry::check())
		{
			return Redirect::route('vedette.home');
//			$redirect = Session::get('loginRedirect', 'auth.home');

		}
		// Show the page
		return View::make(Config::get('vedette::vedette_views.login'));
	}

    /**
     * Authenticate the user
     *
     * @author Steve Montambeault
     * @link   http://stevemo.ca
     *
     *
     * @return Response
     */
    public function postLogin()
    {
        try
        {
            $remember = Input::get('remember_me', false);
/*
if(!empty($input['rememberMe'])) {
   $user = Sentry::authenticate($credentials, true);
} else {
   $user = Sentry::authenticate($credentials, false);
}
*/

            $userdata = array(
//                Config::get('cartalyst/sentry::users.login_attribute') => Input::get('email'),
                'email' => Input::get('email'),
                'password' => Input::get('password')
            );

            $user = Sentry::authenticate($userdata, $remember);
            Event::fire('users.login', array($user));
            return Redirect::intended(Config::get('vedette::vedette_settings.home_route'))->with('success', trans('lingos::auth.success.login'));
        }
        catch (LoginRequiredException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.login_required'));
        }
        catch (PasswordRequiredException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.password_required'));
        }
        catch (WrongPasswordException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.wrong_password'));
        }
        catch (UserNotActivatedException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.not_activated'));
        }
        catch (UserNotFoundException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.not_found'));
        }
        catch (UserSuspendedException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.suspended'));
        }
        catch (UserBannedException $e)
        {
//            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
            return Redirect::back()->withInput()->with('error',trans('lingos::auth.account.banned'));
        }
    }



	/**
	 * Account sign up.
	 *
	 * @return View
	 */
	public function getRegister()
	{
		// Is the user logged in?
		if (Sentry::check())
		{
			return Redirect::route('account');
		}

		// Show the page
		return View::make(Config::get('vedette::vedette_views.register'));
	}

	/**
	 * Account sign up form processing.
	 *
	 * @return Redirect
	 */
	public function postRegister()
	{
		// Declare the rules for the form validation
		$rules = array(
			'first_name'       => 'required|min:3',
			'last_name'        => 'required|min:3',
			'email'            => 'required|email|unique:users',
			'email_confirm'    => 'required|email|same:email',
			'password'         => 'required|between:4,255',
			'confirm_password' => 'required|same:password',
		);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		try
		{
			// Register the user
			$user = Sentry::register(array(
				'first_name' => Input::get('first_name'),
				'last_name'  => Input::get('last_name'),
				'email'      => Input::get('email'),
				'password'   => Input::get('password'),
			));

			// Data to be used on the email view
			$data = array(
				'user'          => $user,
				'activationUrl' => URL::route('activate', $user->getActivationCode()),
			);

			// Send the activation code through email
			Mail::send('emails.register-activate', $data, function($m) use ($user)
			{
				$m->to($user->email, $user->first_name . ' ' . $user->last_name);
				$m->subject('Welcome ' . $user->first_name);
			});

			// Redirect to the register page
			return Redirect::back()->with('success', trans('lingos::auth.success.signup'));
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
			$this->messageBag->add('email', trans('lingos::auth.account.already_exists'));
		}

		// Ooops.. something went wrong
		return Redirect::back()->withInput()->withErrors($this->messageBag);
	}

	/**
	 * User account activation page.
	 *
	 * @param  string  $actvationCode
	 * @return
	 */
	public function getActivate($activationCode = null)
	{
		// Is the user logged in?
		if (Sentry::check())
		{
			return Redirect::route('account');
		}

		try
		{
			// Get the user we are trying to activate
			$user = Sentry::getUserProvider()->findByActivationCode($activationCode);

			// Try to activate this user account
			if ($user->attemptActivation($activationCode))
			{
				// Redirect to the login page
				return Redirect::route('signin')->with('success', trans('lingos::auth.success.activate'));
			}

			// The activation failed.
			$error = trans('lingos::auth.error.activate');
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$error = trans('lingos::auth.error.activate');
		}

		// Ooops.. something went wrong
		return Redirect::route('signin')->with('error', $error);
	}

	/**
	 * Forgot password page.
	 *
	 * @return View
	 */
	public function getForgotPassword()
	{
		// Show the page
//		return View::make('frontend.auth.forgot-password');
		return View::make(Config::get('vedette::vedette_views.forgot'));
	}

	/**
	 * Forgot password form processing page.
	 *
	 * @return Redirect
	 */
	public function postForgotPassword()
	{
		// Declare the rules for the validator
		$rules = array(
			'email' => 'required|email',
		);

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::route('vedette.forgot-password')->withInput()->withErrors($validator);
		}

		try
		{
			// Get the user password recovery code
			$user = Sentry::getUserProvider()->findByLogin(Input::get('email'));

			// Data to be used on the email view
			$data = array(
				'user'              => $user,
				'forgotPasswordUrl' => URL::route('forgot-password-confirm', $user->getResetPasswordCode()),
			);
			// Send the activation code through email
//			Mail::send('emails.forgot-password', $data, function($m) use ($user)
			Mail::send(Config::get('vedette::vedette_views.forgot_password'), $data, function($m) use ($user)
			{
				$m->to($user->email, $user->first_name . ' ' . $user->last_name);
//				$m->subject('Account Password Recovery');
				$m->subject( trans('lingos::auth.account_password_recovery') );
			});
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			// Even though the email was not found, we will pretend
			// we have sent the password reset code through email,
			// this is a security measure against hackers.
		}

		//  Redirect to the forgot password
		return Redirect::route('vedette.forgot-password')->with('success', trans('lingos::auth.success.forgot-password'));
	}

	/**
	 * Forgot Password Confirmation page.
	 *
	 * @param  string  $passwordResetCode
	 * @return View
	 */
	public function getForgotPasswordConfirm($passwordResetCode = null)
	{
		try
		{
			// Find the user using the password reset code
			$user = Sentry::getUserProvider()->findByResetPasswordCode($passwordResetCode);
		}
		catch(Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			// Redirect to the forgot password page
			return Redirect::route('vedette.forgot-password')->with('error', trans('lingos::auth.account.not_found'));
		}

		// Show the page
//		return View::make('frontend.auth.forgot-password-confirm');
		return View::make(Config::get('vedette::vedette_views.forgot_confirm'));
	}

	/**
	 * Forgot Password Confirmation form processing page.
	 *
	 * @param  string  $passwordResetCode
	 * @return Redirect
	 */
	public function postForgotPasswordConfirm($passwordResetCode = null)
	{
		// Declare the rules for the form validation
		$rules = array(
			'password'         => 'required',
			'confirm_password' => 'required|same:password'
		);

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::route('forgot-password-confirm', $passwordResetCode)->withInput()->withErrors($validator);
		}

		try
		{
			// Find the user using the password reset code
			$user = Sentry::getUserProvider()->findByResetPasswordCode($passwordResetCode);

			// Attempt to reset the user password
			if ($user->attemptResetPassword($passwordResetCode, Input::get('password')))
			{
				// Password successfully reseted
				return Redirect::route('signin')->with('success', trans('lingos::auth.success.reset_password'));
			}
			else
			{
				// Ooops.. something went wrong
				return Redirect::route('signin')->with('error', trans('lingos::auth.error.reset_password'));
			}
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			// Redirect to the forgot password page
			return Redirect::route('vedette.forgot-password')->with('error', trans('lingos::auth.account.not_found'));
		}
	}

	/**
	 * Logout page.
	 *
	 * @return Redirect
	 */
	public function getLogout()
	{
		// Log the user out
		Sentry::logout();

		// Redirect to the users page
		return Redirect::route('vedette.home')->with('success', trans('lingos::auth.success.logout'));
	}

}
