<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Products;
use App\Models\Opsikirim;
use App\Models\Pembayaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MetodePembayaran;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = Cart::where('id_user',auth()->user()->id)->first();
        $products = Order::where('id_cart',$cart->id)->with(['cart','opsikirim','metodepembayaran','status_order'])->get();

        return response([
            'data' => $products,
        ], 200);
    }

    public function payment(){
        
        $opsiKirim = Opsikirim::all();

        $metode_pembayaran = MetodePembayaran::all();

        $user = User::where('id',auth()->user()->id)->first();

        $data = [
            'opsiKirim' => $opsiKirim,
            'user' =>$user,
            'metode_pembayaran' =>$metode_pembayaran
        ];

        return response([
            'message' => "data payment",
            'data' => $data,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    //  order -> status -> 1 
    public function store(Request $request)
    {
        $date = Carbon::now()->format('Ymd');

        $orderController = new OrderController();
        $uniqid = $orderController->generateUniqueId();

        $no_resi = 'INV/'.$date.'/'.$uniqid;

        // sum total harga 
        $cart = Cart::where('id_user',auth()->user()->id)->first();
        $cartItem = CartItem::where('id_cart',$cart->id)->get();
        $jumlah_harga = 0;

        foreach ($cartItem as $key => $item) {
            $product = Products::where('id', $item['id_produk'])->first();
            $jumlah_barang = CartItem::where('id_produk',$product->id)->first();
            $hargaperproduk = $product->harga * $jumlah_barang->jumlah_barang;

            $jumlah_harga = $jumlah_harga + $hargaperproduk;
        }

        $order = Order::create([
            'no_resi' => $no_resi,
            'jumlah_harga' => $jumlah_harga,
            'alamat' => $request->alamat,
            'status_order_id' => 1,
            'id_cart' => $cart->id,
            'id_opsikirim' => $request->opsikirim,
            'id_metode_pembayaran' => $request->metode_pembayaran
        ]);

        // non aktif cart after order 
        foreach ($cartItem as $key => $item) {
            CartItem::where('id', $item['id'])->update(['id_status_cart_items'=>2]);
        }

        $review = Order::where('id',$order->id)->with(['status_order','cart','opsikirim','metodepembayaran'])->get();

        return response([
            'message' => "Berhasil",
            'data' => $review,
        ], 200);
    }
    
    public function generateUniqueId() {
        $uniqid = mt_rand(1000000000, 9999999999);

        $orderController = new OrderController();

        if($orderController->checkUniqueId($uniqid) == true){
            $orderController->generateUniqueId();
        }

        return $uniqid;
    }
    public function checkUniqueId($uniqid) {
        $check = Order::where('no_resi','LIKE','%'.$uniqid.'%')->get();
        if($check->count() > 0){
            return true;
        }
        return false;
    }

    //  order -> status -> 2 

    public function store_pembayaran(Request $request){

        $pembayaran = Pembayaran::create([
            'bukti_pembayaran' => $request->pembayaran,
        ]);

        $idPembayaran = $pembayaran->id;

        $cart = Cart::where('id_user',auth()->user()->id)->first();

        $updateOrder = Order::where('id_cart',$cart->id)->first();
        $updateOrder->status_order_id = 2;
        $updateOrder->pembayaran_id = $idPembayaran;
        $updateOrder->save();

        // pengurangan jumlah produk 
        $cart = Cart::where('id_user',auth()->user()->id)->first();
        $cartItem = CartItem::where('id_cart',$cart->id)->get();
        $jumlah_barang = 0;

        foreach ($cartItem as $key => $item) {
            $product = Products::where('id', $item['id_produk'])->first();
            $jumlah_barang = CartItem::where('id_produk',$product->id)->first();

            $product->jumlah_product = $product->jumlah_product - $jumlah_barang->jumlah_barang;
            $product->save();
        }



        $review = Order::where('id',$updateOrder->id)->with(['status_order','cart','opsikirim','metodepembayaran','pembayaran'])->get();

        return response([
            'message' => "Berhasil",
            'data' => $review,
        ], 200);
    }

    // order -> status -> 4

    public function store_dibatalkan(Request $request,$id){

        $cart = Cart::where('id_user',auth()->user()->id)->first();
        $updateOrder = Order::where('id',$id)->where('id_cart',$cart->id)->first();

        $updateOrder->status_order_id = 4;
        $updateOrder->save();

        $review = Order::where('id',$updateOrder->id)->with(['status_order','cart','opsikirim','metodepembayaran','pembayaran'])->get();

        return response([
            'message' => "Berhasil",
            'data' => $review,
        ], 200);
    }

    // order -> status -> 5

    public function store_waktu_habis(Request $request,$id){

        $cart = Cart::where('id_user',auth()->user()->id)->first();
        $updateOrder = Order::where('id',$id)->where('id_cart',$cart->id)->first();

        $updateOrder->status_order_id = 5;
        $updateOrder->save();

        $review = Order::where('id',$updateOrder->id)->with(['status_order','cart','opsikirim','metodepembayaran','pembayaran'])->get();

        return response([
            'message' => "Berhasil",
            'data' => $review,
        ], 200);
    }

    // order -> status -> 6
    // untuk admin 
    public function store_verifikasi_gagal(Request $request, $id){

        $updateOrder = Order::findOrFail($id);
        $updateOrder->status_order_id = 6;
        $updateOrder->save();

        // penambahan jumlah produk 
        $cart = Cart::where('id_user',auth()->user()->id)->first();
        $cartItem = CartItem::where('id_cart',$cart->id)->get();
        $jumlah_barang = 0;

        foreach ($cartItem as $key => $item) {
            $product = Products::where('id', $item['id_produk'])->first();
            $jumlah_barang = CartItem::where('id_produk',$product->id)->first();

            $product->jumlah_product = $product->jumlah_product + $jumlah_barang->jumlah_barang;
            $product->save();
        }



        $review = Order::where('id',$updateOrder->id)->with(['status_order','cart','opsikirim','metodepembayaran','pembayaran'])->get();

        return response([
            'message' => "Berhasil",
            'data' => $review,
        ], 200);
    }

    // order -> status -> 7

    public function store_selesai(Request $request, $id){

        $updateOrder = Order::findOrFail($id);
        $updateOrder->status_order_id = 7;
        $updateOrder->save();

        $review = Order::where('id',$updateOrder->id)->with(['status_order','cart','opsikirim','metodepembayaran','pembayaran'])->get();

        return response([
            'message' => "Berhasil",
            'data' => $review,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = Cart::where('id_user',auth()->user()->id)->first();

        $review = Order::where('id',$id)->where('id_cart',$cart->id)->with(['status_order','cart','opsikirim','metodepembayaran','pembayaran'])->get();

        return response([
            'data' => $review,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
