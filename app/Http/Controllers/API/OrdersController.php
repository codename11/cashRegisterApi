<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order;
use App\Article;
use App\Order_Details;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Http\Resources\OrderDetailsResource;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('user', 'order_details')->get();

        $response = array(
            "orders" => OrderResource::collection($orders),
            "message" => "Retrieved successfully"
        );

        return response($response, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {

        $data = $request->all();

        $validator = Validator::make($data, [
            "articles.*.article_id" => "required|numeric",
            "articles.*.quantity" => "required|numeric"
        ]);

        $ordersAndDetails = new \stdClass;

        if($validator->fails()){

            $response = array(
                "Errors" => $validator->errors(),
            );

            return response()->json($response);

        }
        else{

            $len = count($data["articles"]);
            $ordersAndDetails->user = User::find($data["userId"]);
            $ordersAndDetails->orders = [];
            $ordersAndDetails->order_details = [];

            for($i=0;$i<$len;$i++){

                $order = new Order;
                $order->user_id = $data["userId"];
                $article = Article::find($data["articles"][$i]["article_id"]);
                $order->total_price = (int)$data["articles"][$i]["quantity"]*(float)$article->price;
                $order->save();

                $order_details = new Order_Details;
                $order_details->order_id = $order->id;
                $order_details->article_id = $article->id;
                $order_details->quantity = (int)$data["articles"][$i]["quantity"];
                $order_details->save();

                $ordersAndDetails->orders[$i] = new OrderResource($order);
                $ordersAndDetails->order_details[$i] = new OrderDetailsResource($order_details);

            }

            $response = array(
                "message" => "Stored successfully",
                "ordersAndDetails" => $ordersAndDetails, 
            );

        }
        
        return response()->json($response);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $order = Order::find($id)->with('user', 'order_details')->get();

        $response = array(
            "message" => "Retrieved successfully",
            "order" => new OrderResource($order), 
        );

        return response($response, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $order = Order::find($id);
        $order->user_id = $request->user_id;
        $order->save();

        $response = array(
            "message" => "Updated successfully",
            "order" => new OrderResource($order), 
        );

        return response($response, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $order = Order::find($id);
        $deletedOrder = $order;
        $order->delete();

        $response = array(
            "message" => "Deleted successfully",
            "order" => new OrderResource($deletedOrder), 
        );

        return response($response, 200);
        
    }
}
