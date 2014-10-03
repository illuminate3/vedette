<?php namespace Vedette\controllers;

use Vedette\models\User as User;
use Auth;
use View;
use Session;

class AdminController extends \BaseController {

	/**
	 * Pallet Repository
	 *
	 * @var Pallet
	 */
	protected $pallet;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Display an admin index view.
	 *
	 * @return Response
	 */
	public function index()
	{

//$data = Session::all();
//dd($data);
		$countUsers = $this->user->countUsers();

//		return View::make('admin.index');
		return View::make('admin.index', compact(
			'countUsers'
		));

	}

	/**
	 * Display a not found view.
	 *
	 * @return Response
	 */
	public function notfound()
	{
		return View::make('errors.404');
	}

}
