<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\PostDataTable;
use App\Facades\UtilityFacades;
use App\Models\Category;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function index(PostDataTable $dataTable)
    {
        if (Auth::user()->can('manage-blog')) {
            return $dataTable->render('admin.posts.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        $settingData    = UtilityFacades::getsettings('plan_setting');
        $plans          = json_decode($settingData, true);
        $post           = Posts::all()->count();
        if ($post < $plans['max_blogs']) {
            if (Auth::user()->can('create-blog')) {
                $category   = Category::where('status', 1)->pluck('name', 'id');
                return  view('admin.posts.create', compact('category'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Please update your plan because blogs limit low.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create-blog')) {
            request()->validate([
                'title'         => 'required|max:100',
                'photo'         => 'required',
                'description'   => 'required',
                'category_id'   => 'required',
            ]);
            if ($request->hasFile('photo')) {
                $request->validate([
                    'photo'     => 'required',
                ]);
                $path   = $request->file('photo')->store('posts');
            }
            Posts::create([
                'title'             => $request->title,
                'description'       => $request->description,
                'category_id'       => $request->category_id,
                'photo'             => $path,
                'short_description' => $request->short_description,
                'created_by'        => Auth::user()->id,
            ]);
            return redirect()->route('blogs.index')->with('success', __('Post created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('edit-blog')) {
            $posts      = Posts::find($id);
            $category   = Category::where('status', 1)->pluck('name', 'id');
            return  view('admin.posts.edit', compact('posts', 'category'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('edit-blog')) {
            request()->validate([
                'title'         => 'required|max:100',
                'description'   => 'required',
                'category_id'   => 'required',
            ]);
            $post   = Posts::find($id);
            if ($request->hasFile('photo')) {
                $path           = $request->file('photo')->store('posts');
                $post->photo    = $path;
            }
            $post->title                = $request->title;
            $post->description          = $request->description;
            $post->category_id          = $request->category_id;
            $post->short_description    = $request->short_description;
            $post->created_by           = Auth::user()->id;
            $post->save();
            return redirect()->route('blogs.index')->with('success', __('Posts updated successfully'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete-blog')) {
            $post = Posts::find($id);
            $post->delete();
            return redirect()->route('blogs.index')->with('success', __('Posts deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName         = $request->file('upload')->getClientOriginalName();
            $fileName           = pathinfo($originName, PATHINFO_FILENAME);
            $extension          = $request->file('upload')->getClientOriginalExtension();
            $fileName           = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move(public_path('images'), $fileName);
            $CKEditorFuncNum    = $request->input('CKEditorFuncNum');
            $url                = asset('images/' . $fileName);
            $msg                = 'Image uploaded successfully';
            $response           = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function viewBlog($slug)
    {
        $lang       = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        $blog       =  Posts::where('slug', $slug)->first();
        if (!$blog) {
            abort(404);
        }
        $allBlogs   =  Posts::all();
        return view('admin.posts.view-blog', compact('blog', 'allBlogs', 'lang'));
    }

    public function seeAllBlogs(Request $request)
    {
        $lang           = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        if ($request->category_id != '') {
            $allBlogs = Posts::where('category_id', $request->category_id)->paginate(3);
            $blogHtml = '';
            foreach ($allBlogs as $blog) {
                $imageUrl = $blog->photo ? Storage::url(tenant('id') . '/' . $blog->photo) : asset('vendor/landing-page2/image/blog-card-img-2.png');
                $redirectUrl = route('view.blog', $blog->slug);
                $createdAt = UtilityFacades::date_time_format($blog->created_at);
                $shortDescription = $blog->short_description ? $blog->short_description : 'A step-by-step guide on how to configure and implement multi-tenancy in a Laravel application, including tenant isolation and database separation.';
                $blogHtml .= '<div class="article-card">
                    <div class="article-card-inner">
                        <div class="article-card-image">
                            <a href="#">
                                <img src="' . $imageUrl . '" alt="blog-card-image">
                            </a>
                        </div>
                        <div class="article-card-content">
                            <div class="author-info d-flex align-items-center justify-content-between">
                                <div class="date d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                        <!-- SVG path for date icon -->
                                    </svg>
                                    <span>' . $createdAt . '</span>
                                </div>
                            </div>
                            <h3>
                                <a href="' . $redirectUrl . '">' . $blog->title . '</a>
                            </h3>
                            <p>' . $shortDescription . '</p>
                        </div>
                    </div>
                </div>';
            }
            return response()->json(['appendedContent' => $blogHtml]);
        } else {
            $allBlogs = Posts::paginate(3);
        }
        $recentBlogs    = Posts::latest()->take(3)->get();
        $lastBlog       = Posts::latest()->first();
        $categories     = Category::all();
        return view('admin.posts.view-all-blogs', compact('allBlogs', 'recentBlogs', 'lastBlog', 'categories', 'lang'));
    }
}
