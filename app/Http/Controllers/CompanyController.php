<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\CompaniesDataTable;

class CompanyController extends Controller
{
    public function index(CompaniesDataTable $dataTable)
    {
        if (Auth::user()->can('manage-company')) {
        return $dataTable->render('companies.index');
        } else {
            return redirect()->back()->with('failed', __('Permission Denied.'));
        }
    }

    public function create()
    {
       if (\Auth::user()->can('create-company')) {
        $category = Company::where('tenant_id',tenant('id'))->get();
        return view('companies.create', compact('category'));
       } else {
           return redirect()->back()->with('failed', __('Permission Denied.'));
       }
    }

    public function store(Request $request)
    {
       if (\Auth::user()->can('create-company')) {
        request()->validate([
            'name' => 'required',
            'status' => 'required',
        ]);
        Company::create([
            'name' => $request->name,
            'status' => $request->status
        ]);
       return redirect()->route('companies.index')->with('success', __('Category Created Successfully'));
       } else {
           return redirect()->back()->with('failed', __('Permission Denied.'));
       }
    }

    public function edit($id)
    {
       if (Auth::user()->can('edit-company')) {

        $company = Company::find($id);
        return view('companies.edit', compact('company'));
       } else {
           return redirect()->back()->with('failed', __('Permission Denied.'));
        }
    }

    public function update(Request $request, $id)
    {

        dd($id);
        if (Auth::user()->can('edit-company')) {
        request()->validate([
            'company_name' => 'required',
            'status' => 'required',
        ]);
        $category = Company::find($id);
        $category->company_name = $request->company_name;
        $category->address = $request->address;
        $category->contact_person_name = $request->contact_person_name;
        $category->contact_person_telephone = $request->contact_person_telephone;
        $category->email = $request->email;
        $category->subscription_start_date = $request->subscription_start_date;
        $category->subscription_end_date = $request->subscription_end_date;
        $category->alternative_email = $request->alternative_email;
        $category->tin = $request->tin;
        $category->update();
        return redirect()->route('company.index')->with('success', __('Company Updated Successfully'));
       } else {
           return redirect()->back()->with('failed', __('Permission Denied.'));
       }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete-company')) {

        $category = Company::find($id);
        $category->delete();
        return redirect()->route('companies.index')->with('success', __('Category Deleted Successfully'));
        } else {
           return redirect()->back()->with('failed', __('Permission Denied.'));
       }
    }
}
