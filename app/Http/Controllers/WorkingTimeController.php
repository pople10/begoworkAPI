<?php

namespace App\Http\Controllers;

use App\Models\WorkingTime;
use App\Models\Listing;
use Illuminate\Http\Request;

class WorkingTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\WorkingTime  $workingTime
     * @return \Illuminate\Http\Response
     */
    public function show(WorkingTime $workingTime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkingTime  $workingTime
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkingTime $workingTime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkingTime  $workingTime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkingTime $workingTime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkingTime  $workingTime
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkingTime $workingTime)
    {
        //
    }
    
    public function getWorkingTimeByListingID($idListing)
    {
        if(Listing::find($idListing)==NULL)
            return response()->json([
                'erreur' => "Listing n'existe pas"
            ], 422);
        $data = WorkingTime::where("idListing",$idListing)->get();
        if(count($data)==0)
            return response()->json([
                'erreur' => "Temps du travail n'existe pas"
            ], 422);
        return response()->json($data);
    }
}
