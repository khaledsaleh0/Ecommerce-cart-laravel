<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Promo;
use Illuminate\Http\Request;
use App\Models\Package;

// Packages CartController
class CartController extends Controller
{
    public function addToCart($package_id) // add to cart
    {
        $package = Package::find($package_id);
        if(!$package) {
            abort(404);
        }
        $cart = session()->get('cart');
 
        if(!$cart) {
            $cart = [
                    $package_id => [
                        "name" => $package->name,
                        "price" => $package->price,
                        "description" => $package->description
                    ]
            ];
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Successfully added to cart!');
        }
 
        $cart[$package_id] = [
            "name" => $package->name,
            "price" => $package->price,
            "description" => $package->description
        ];
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Successfully added to cart!');
    }

    public function removeFromCart($id) //remove from cart
    {
        if($id) {
            $cart = session()->get('cart');
            if(isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Removed from cart!');
        }
    
    }

    public function applyPromo(Request $r) // promocode integration
    {
        $code = $r->promocode;
        $promo = Promo::where('code',$code)->first();
        $percentage = 0;
        if(!$promo) {
            return view('front.cart.checkout', compact('percentage'));
        }
        else
        {
            $percentage = $promo->percentage;
            return view('front.cart.checkout', compact('percentage'));
        }
    }

    public function fawry(Request $request) // Fawry integration
    {
        $pack_id = $request->pack_id;
        $this->fawry = new Fawry();
        $package = Package::findOrFail($pack_id);
        $this->fawry->set_package($package);
        $str = $this->fawry->build_request();
        return redirect($str);
    }

}
