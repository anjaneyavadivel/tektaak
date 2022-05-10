<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CommissionHistory;
use App\Models\Wallet;
use App\Models\Seller;
use App\Models\User;
use App\Models\Search;
use Auth;

class ReportController extends Controller
{
    public function stock_report(Request $request)
    {
       $sort_by = $request->category_id;
		if($sort_by==0){
        $sort_by = null;
		}else{
			$sort_by = $request->category_id;
		}
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            if($sort_by!=0){
            $products = $products->where('category_id', $sort_by);
			}
        }
        $products = $products->paginate(15);
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'Product.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('#', 'Product Name', 'Num of Sale');

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $key => $product) {
                $row['#']  = ($key+1) + ($products->currentPage() - 1)*$products->perPage();
                $row['Product Name']    = $product->getTranslation('name');
                $row['Num of Sale']    = $product->num_of_sale;
                fputcsv($file, array($row['#'], $row['Product Name'], $row['Num of Sale']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv
        return view('backend.reports.stock_report', compact('products','sort_by'));
    }

    public function in_house_sale_report(Request $request)
    {
		$sort_by = $request->category_id;
		if($sort_by==0){
        $sort_by = null;
		}else{
			$sort_by = $request->category_id;
		}
		$products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('category_id')){
			if($sort_by!=0){
            $products = $products->where('category_id', $sort_by);
			}
        }
        $products = $products->paginate(15);
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'Product.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('#', 'Product Name', 'Num of Sale');

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $key => $product) {
                $row['#']  = ($key+1) + ($products->currentPage() - 1)*$products->perPage();
                $row['Product Name']    = $product->getTranslation('name');
                $row['Num of Sale']    = $product->num_of_sale;
                fputcsv($file, array($row['#'], $row['Product Name'], $row['Num of Sale']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv 
        return view('backend.reports.in_house_sale_report', compact('products','sort_by','roport_id'));
    }

    public function seller_sale_report(Request $request)
    {
        $sort_by =null;
        //$sellers = User::where('user_type', 'seller')->orderBy('created_at', 'desc');
		$sellers = User::select('users.*')
					->join('sellers', function ($join) {
					$join->on('users.id', '=', 'sellers.user_id');
				});
//->get();
			//dd($sellers);
        if ($request->has('verification_status')){
            $sort_by = $request->verification_status;
            $sellers = $sellers->where('sellers.verification_status', $sort_by);
        }
        $sellers = $sellers->paginate(10);
		
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'Product.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('Seller Name', 'Shop Name', 'Number of Product Sale', 'Order Amount');

        $callback = function() use($sellers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sellers as $key => $seller) {
				$num_of_sale = 0;
				foreach ($seller->products as $key => $product) {
					$num_of_sale += $product->num_of_sale;
				}
                $row['Seller Name']  = $seller->name;
                $row['Shop Name']    = $seller->shop->name;
                $row['Number of Product Sale']    = $num_of_sale;
				$row['Order Amount']    = single_price(\App\Models\OrderDetail::where('seller_id', $seller->id)->sum('price'));
                fputcsv($file, array($row['Seller Name'], $row['Shop Name'], $row['Number of Product Sale'], $row['Order Amount']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv 
		
        return view('backend.reports.seller_sale_report', compact('sellers','sort_by'));
    }

    public function wish_report(Request $request)
    {
        $sort_by = $request->category_id;
		if($sort_by==0){
        $sort_by = null;
		}else{
			$sort_by = $request->category_id;
		}
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            if($sort_by!=0){
            $products = $products->where('category_id', $sort_by);
			}
        }
        $products = $products->paginate(10);
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'Product.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('#', 'Product Name', 'Num of Wish');

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $key => $product) {
                $row['#']  = ($key+1) + ($products->currentPage() - 1)*$products->perPage();
                $row['Product Name']    = $product->getTranslation('name');
                $row['Num of Wish']    = $product->wishlists->count();
                fputcsv($file, array($row['#'], $row['Product Name'], $row['Num of Wish']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv
        return view('backend.reports.wish_report', compact('products','sort_by'));
    }

    public function user_search_report(Request $request){
        $searches = Search::orderBy('count', 'desc')->paginate(10);
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'Search.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('#', 'Search By', 'Number searches');

        $callback = function() use($searches, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($searches as $key => $searche) {
                $row['#']  = ($key+1) + ($searches->currentPage() - 1)*$searches->perPage();
                $row['Search By']    = $searche->query;
                $row['Number searches']    = $searche->count;
                fputcsv($file, array($row['#'], $row['Search By'], $row['Number searches']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv
        return view('backend.reports.user_search_report', compact('searches','roport_id'));
    }
    
    public function commission_history(Request $request) {
        $seller_id = null;
        $date_range = null;
        
        if(Auth::user()->user_type == 'seller') {
            $seller_id = Auth::user()->id;
        } if($request->seller_id) {
            $seller_id = $request->seller_id;
        }
        
        $commission_history = CommissionHistory::orderBy('created_at', 'desc');
        
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id){
            
            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }
        
        $commission_history = $commission_history->paginate(10);
		
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'commission.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('#', 'Order Code', 'Admin Commission', 'Seller Earning', 'Created At');

        $callback = function() use($commission_history, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($commission_history as $key => $history) {
                $row['#']  = ($key+1);
                $row['Order Code']    = $history->order->code;
                $row['Admin Commission']    = $history->admin_commission;
                $row['Seller Earning']    = $history->seller_earning;
                $row['Created At']    = $history->created_at;
                fputcsv($file, array($row['#'], $row['Search By'], $row['Order Code'], $row['Admin Commission'], $row['Seller Earning'], $row['Created At']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv
		
        if(Auth::user()->user_type == 'seller') {
            return view('seller.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
        }
        return view('backend.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
    }
    
    public function wallet_transaction_history(Request $request) {
        $user_id = null;
        $date_range = null;
        
        if($request->user_id) {
            $user_id = $request->user_id;
        }
        
        $users_with_wallet = User::whereIn('id', function($query) {
            $query->select('user_id')->from(with(new Wallet)->getTable());
        })->get();

        $wallet_history = Wallet::orderBy('created_at', 'desc');
        
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $wallet_history = $wallet_history->where('created_at', '>=', $date_range1[0]);
            $wallet_history = $wallet_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($user_id){
            $wallet_history = $wallet_history->where('user_id', '=', $user_id);
        }
        
        $wallets = $wallet_history->paginate(10);
		
		//Export CSV
		$roport_id = $request->input('roport_id') ?? '';
		if ($roport_id==1){
            $fileName = 'wallets.csv';
			$headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
			);
        $columns = array('#', 'Customer', 'Date', 'Amount', 'Payment method', 'Approval');

        $callback = function() use($wallets, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($wallets as $key => $wallet) {
				if ($wallet->offline_payment){
				if ($wallet->approval){
				$wallet_status = 'Approved';
				}else{
				$wallet_status = 'Pending';  
				}
				}else{
				$wallet_status = 'N/A';         
				}
                $row['#']  = ($key+1);
                $row['Customer']    = $wallet->user->name;
                $row['Date']    = date('d-m-Y', strtotime($wallet->created_at));
                $row['Amount']    = single_price($wallet->amount);
                $row['Payment method']    = ucfirst(str_replace('_', ' ', $wallet ->payment_method));
                $row['Approval']    = $wallet_status;
                fputcsv($file, array($row['#'], $row['Customer'], $row['Date'], $row['Amount'], $row['Payment method'], $row['Approval']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        }
		//End Export csv
		
        return view('backend.reports.wallet_history_report', compact('wallets', 'users_with_wallet', 'user_id', 'date_range'));
    }
}
