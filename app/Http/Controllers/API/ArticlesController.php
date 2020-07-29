<?php

namespace App\Http\Controllers\API;

use App\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ArticleResource;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::all();

        $response = array(
            "articles" => ArticleResource::collection($articles),
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
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            "name" => "required|max:30",
            "barcode" => "required|max:255",
            "price" => "required|numeric",
        ]);

        $response = array();

        if($validator->fails()){

            $response["error"] = $validator->errors();
            $response["message"] = "Validation Error";

            return response($response);

        }

        $article = Article::create($data);
        $response["article"] = new ArticleResource($article);
        $response["message"] = "Created successfully";

        return response($response, 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Article::find($id);
        $response = array();
        $response["article"] = new ArticleResource($article);
        $response["message"] = "Retrieved successfully";

        return response($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        $article->update($request->all());

        $response = array();
        $response["article"] = new ArticleResource($article);
        $response["message"] = "Updated successfully";

        return response($response, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = Article::find($id);
        $deletedArticle = $article;
        $article->delete();

        $response = array();
        $response["article"] = new ArticleResource($deletedArticle);
        $response["message"] = "Deleted successfully";

        return response($response);

    }
    
}
