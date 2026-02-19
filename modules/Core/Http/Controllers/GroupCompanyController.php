<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Models\GroupCompany;

class GroupCompanyController extends BaseController
{
    public function index() { $grupos = GroupCompany::all(); return view('core::group_companies.index', compact('grupos')); }
    public function create() { return view('core::group_companies.create'); }
    public function store(Request $r) { GroupCompany::create($r->validate(['nombre'=>'required','descripcion'=>'nullable'])); return redirect()->route('core.group_companies.index')->with('status','created'); }
    public function show(GroupCompany $grupo) { return view('core::group_companies.show', compact('grupo')); }
    public function edit(GroupCompany $grupo) { return view('core::group_companies.edit', compact('grupo')); }
    public function update(Request $r, GroupCompany $grupo) { $grupo->update($r->validate(['nombre'=>'required','descripcion'=>'nullable'])); return redirect()->route('core.group_companies.index')->with('status','updated'); }
    public function destroy(GroupCompany $grupo) { $grupo->delete(); return back()->with('status','deleted'); }
}
