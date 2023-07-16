<?php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function createArticle(Request $request)
    {
        $messages = [
            'title.required' => 'title is Required!',
            'content.required' => 'content is Required!',
            'author.required' => 'author is Required!',
            'category.required' => 'category is Required!',
            'published_at.required' => 'published_at is Required!',
        ];
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:30',
            'content' => 'required',
            'author' => 'required',
            'category' => 'required',
            'published_at' => 'required|date',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all()], 422);
        }


        $article = new Article([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author' => $request->input('author'),
            'category' => $request->input('category'),
            'published_at' => $request->input('published_at'),
        ]);


        $article->save();

        return response()->json($article, 201);
    }

    public function getAllArticles()
    {
        $articles = Article::orderBy('id')->get();
        return response()->json($articles, 200);
    }

    public function getArticle($id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json([], 404);
        }
        return response()->json($article, 200);
    }

    public function updateArticle(Request $request, $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json([], 404);
        }

        $request->validate([
            'title' => 'max:30',
            'published_at' => 'date',
        ]);

        $article->update($request->only(['title', 'content', 'author', 'category', 'published_at']));

        return response()->json($article, 200);
    }

    public function deleteArticle($id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json([], 404);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], 200);
    }
}
