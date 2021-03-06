<?php namespace Vedette\controllers;

use Auth, View, Session, App;
use Bootstrap;

//
use Third\models\Pallet as Pallet;
use Third\models\Catalog as Catalog;
use Third\models\Customer as Customer;
use Third\models\Customer_item as Customer_item;
use Third\models\Item as Item;
use Third\models\Rack as Rack;
use Third\models\Pick as Pick;
use Third\models\Alert as Alert;


class IndexController extends \BaseController {

	public function __construct(
		Customer_item $customer_item,
		Item $item,
		Pick $pick,
		Pallet $pallet
		)
		{
			$this->customer_item = $customer_item;
			$this->item = $item;
			$this->pick = $pick;
			$this->pallet = $pallet;
		}

	/**
	 * Display an admin index view.
	 *
	 * @return Response
	 */
	public function index($slug = '/')
	{

		$pallet_count = count(Pallet::all());
		$item_count = count($this->item->countPalletContents());
		$catalog_count = count(Catalog::all());
		$rack_count = count(Rack::all());
		$pick_count = count($this->pick->countOpenPicks());
		$customer_count = count(Customer::all());
		$alert_count =  count(Alert::all());
		$customer_item_count = count($this->customer_item->countPalletContents());
//dd($customer_item_count);

//		$alerts = Alert::all();
		$alerts = Alert::with('customer')->get();
//dd($alerts);

		return View::make('index',
			compact(
				'pallet_count',
				'item_count',
				'catalog_count',
				'customer_count',
				'customer_item_count',
				'rack_count',
				'pick_count',
				'alert_count',
				'alerts'
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
