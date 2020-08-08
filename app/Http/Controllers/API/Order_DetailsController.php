<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order_Details;
use App\Article;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Validator;

class Order_DetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $order_details = Order_Details::with('orders', 'articles')->get();

        $response = array(
            "order_details" => OrderDetailsResource::collection($order_details),
            "message" => "Retrieved successfully",
        );

        return response($response, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order_Details  $order_Details
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $order_details = Order_Details::with('orders', 'articles')->find($id);

        $response = array(
            "message" => "Retrieved successfully",
            "order_details" => new OrderDetailsResource($order_details), 
        );

        return response($response, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order_Details  $order_Details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $order_details = Order_Details::find($id);

        $oldQuantity = (int)$order_details->quantity;

        $oldArticle = Article::find($order_details->article_id);
        $oldArticlePrice = (float)$oldArticle->price;

        $order = Order::find($order_details->order_id);

        $newArticle = Article::find($request->article_id);

        $oldPrice = (float)$order->total_price;
        $newPrice = $oldPrice - ($oldArticlePrice*$oldQuantity) + (float)$newArticle->price*(int)$request->quantity;
        
        $order_details->article_id = $request->article_id;
        $order_details->quantity = $request->quantity;
        $order_details->save();

        $order = Order::find($order_details->order_id);
        $order->total_price = $newPrice;
        $order->save();

        $response = array(
            "message" => "Updated successfully",
            "order_details" => new OrderDetailsResource($order_details), 
            "order" => new OrderResource($order),
        );

        return response($response, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order_Details  $order_Details
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order_details = Order_Details::find($id);
        $deletedOrder_details = $order_details;
        $order_details->delete();

        $oldArticle = Article::find($deletedOrder_details->article_id);
        $oldQuantity = $order_details->quantity;
        $oldPrice = (float)$oldArticle->price*(int)$oldQuantity;

        $order = Order::find($deletedOrder_details->order_id);
        $order->total_price = (float)$order->total_price - (float)$oldPrice;

        $standingOrder = null;
        if((float)$order->total_price == 0){

            $standingOrder = $order;
            $order->delete();

        }
        
        if((float)$order->total_price > 0){

            $standingOrder = $order;
            $order->save();

        }

        $response = array(
            "message" => "Deleted successfully",
            "order_details" => new OrderDetailsResource($deletedOrder_details), 
            "order" => new OrderResource($standingOrder),
        );

        return response($response, 200);

    }
}
