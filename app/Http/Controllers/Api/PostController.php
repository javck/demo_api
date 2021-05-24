<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;
use App\Models\PostTag;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostController extends Controller
{

    //用於生成 JSON 字串
    private function makeJson($status, $data = null, $msg = null)
    {
        //轉 JSON 時確保中文不會變成 Unicode
        return response()->json(['status' => $status, 'data' => $data, 'message' => $msg])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    private function postValidate(Request $request, $mode = 'store')
    {
        if ($mode == 'update') {
            $rule = [
                'title' => 'max:100',
                'pic' => 'url|max:255',
                'sort' => 'numeric',
                'enabled' => Rule::in([1, 0]),
            ];
        } else {
            $rule = [
                'title' => 'required|max:100',
                'category_id' => 'required|numeric',
                'content' => 'required',
                'pic' => 'required|url|max:255',
                'sort' => 'numeric',
                'enabled' => Rule::in([1, 0]),
            ];
        }
        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            //我的錯誤處理
            return $validator->errors()->first();
        } else {
            return null;
        }
    }

    private function postTagValidate(Request $request)
    {
        $rule = [
            'post_id' => 'required|numeric',
            'tag_id' => 'required||numeric',
            'mode' => Rule::in(['attach', 'detach']),
        ];
        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            //我的錯誤處理
            return $validator->errors()->first();
        } else {
            return null;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::where('enabled', true)->orderBy('sort', 'asc')->get();
        if ($posts && count($posts) > 0) {
            return $this->makeJson(1, $posts);
        } else {
            return $this->makeJson(0, null, '找不到文章資料');
        }
    }

    public function query(Request $request)
    {
        $posts = Post::where('enabled', true)->where('title', 'like', '%' . $request->s . '%')->orderBy('sort', 'asc')->get();
        if ($posts && count($posts) > 0) {
            return $this->makeJson(1, $posts);
        } else {
            return $this->makeJson(0, null, '找不到文章資料');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = $this->postValidate($request);
        if (isset($message)) {
            return $this->makeJson(0, null, $message);
        } else {
            $data = $request->only(['title', 'category_id', 'content', 'pic', 'sort', 'enabled']);
            $post = Post::create($data);
            if (isset($post)) {
                return $this->makeJson(1, ['post_id' => $post->id]);
            } else {
                return $this->makeJson(0, null, '文章建立失敗!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $post = Post::find($id);
        $data = $post->toArray();
        if (isset($post)) {
            if ($request->has('show_category') && $request->show_category == 1) {
                $category = $post->category;
                $data_category = $category->toArray();
                $data['category'] = $data_category;
                return $this->makeJson(1, $data);
            } else {
                return $this->makeJson(1, $data);
            }
        } else {
            return $this->makeJson(0, null, '找不到此文章');
        }
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
        $post = Post::find($id);
        if (isset($post)) {
            $message = $this->postValidate($request, 'update');
            if (!isset($message)) {
                $updatedRow = $post->update($request->only(['title', 'content', 'category_id', 'pic', 'sort', 'enabled']));
                if ($updatedRow == 1) {
                    return $this->makeJson(1, ['post_id' => $post->id]);
                } else {
                    return $this->makeJson(0, null, '文章更新失敗');
                }
            } else {
                return $this->makeJson(0, null, $message);
            }
        } else {
            return $this->makeJson(0, null, '找不到此文章');
        }
    }

    public function updateTag(Request $request)
    {
        $message = $this->postTagValidate($request);
        if (!$message) {
            $post = Post::find($request->post_id);
            if (!isset($post)) {
                return $this->makeJson(0, null, '找不到此文章');
            }
            $tag = Tag::find($request->tag_id);
            if (!isset($tag)) {
                return $this->makeJson(0, null, '找不到此標籤');
            }
            if ($request->mode == 'attach') {
                $isExist = $post->tags->contains($tag->id);
                if ($isExist) {
                    return $this->makeJson(0, null, '文章已存在該標籤');
                }
                $post->tags()->attach($tag->id);
                $postTag = PostTag::contain($post->id, $tag->id)->first();
                if ($postTag) {
                    return $this->makeJson(1, null);
                } else {
                    return $this->makeJson(0, null, '文章新增標籤失敗');
                }
            } else {
                $isExist = $post->tags->contains($tag->id);
                if (!$isExist) {
                    return $this->makeJson(0, null, '文章不存在該標籤');
                }
                $post->tags()->detach($tag->id);
                $postTag = PostTag::contain($post->id, $tag->id)->first();
                if (!$postTag) {
                    return $this->makeJson(1, null);
                } else {
                    return $this->makeJson(0, null, '文章移除標籤失敗');
                }
            }
        } else {
            return $this->makeJson(0, null, $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (isset($post)) {
            $deletedRows = $post->delete();
            if ($deletedRows == 1) {
                return $this->makeJson(1);
            } else {
                return $this->makeJson(0, null, '刪除文章失敗');
            }
        } else {
            return $this->makeJson(0, null, '找不到此文章');
        }
    }
}
