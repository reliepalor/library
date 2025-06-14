<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DojoCat;
use App\Models\Cat;

class CatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cats = Cat::with('dojocat')->orderBy('created_at','desc')->paginate(10);
        return view('cats.index',["cats" => $cats]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dojocats = DojoCat::all();
        return view('cats.create',["dojocats" => $dojocats]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=> 'required|string|max:155',
            'breed'=> 'required|string|max:155',
            'age'=> 'required|integer|max:10',
            'dojocat_id' => 'required|exists:dojo_cats,id',

        ]);

        Cat::create($validated);

        return redirect()->route('cats.index')->with('success', 'Cat added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cats = Cat::with('dojocat')->findOrFail($id);
        return view('cats.show',["cats" => $cats]) ;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
