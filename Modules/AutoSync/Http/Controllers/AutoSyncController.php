<?php

namespace Modules\AutoSync\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AutoSyncActivity;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class AutoSyncController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $errors = AutoSyncActivity::where('msg_category', 'ERROR')->with('autoSync')->orderBy('id', 'DESC')->paginate(12);

        return view('autosync::index',['data' => $errors]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('autosync::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('autosync::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('autosync::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
