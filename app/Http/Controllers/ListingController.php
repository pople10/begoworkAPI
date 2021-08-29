<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Location;
use App\Models\Product;
use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class ListingController extends Controller
{
    public $dayss = [
        'Sunday'=>'Dimanche',
        'Monday'=>'Lundi',
        'Tuesday'=>'Mardi',
        'Wednesday'=>'Mercredi',
        'Thursday'=>'Jeudi',
        'Friday'=>'Vendredi',
        'Saturday'=>'Samedi'
    ];
    
    public $days = [
        'Sun'=>'Sunday',
        'Mon'=>'Monday',
        'Tue'=>'Tuesday',
        'Wed'=>'Wednesday',
        'Thu'=>'Thursday',
        'Fri'=>'Friday',
        'Sat'=>'Saturday'
    ];
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
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show(Listing $listing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function edit(Listing $listing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing)
    {
        //
    }
    
    public function findByTypeID(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'idType' => 'required|numeric'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $data = Listing::all()->where("idType",$request->idType);
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findByKeyword(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'keyword' => 'required|string'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $data = Listing::where("description","LIKE",'%'.$request->keyword.'%')->orWhere("title","LIKE",'%'.$request->keyword.'%')->get();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findByCity(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'city' => 'required|string'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $arrayHelper = array();
        foreach(Location::where("city",$request->city)->get() as $val)
        {
            $arrayHelper[] = $val->idLocation;
        }
        $data = Listing::whereIn("idLocation",$arrayHelper)->get();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findByCountry(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'country' => 'required|string'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $arrayHelper = array();
        foreach(Location::where("country",$request->country)->get() as $val)
        {
            $arrayHelper[] = $val->idLocation;
        }
        $data = Listing::whereIn("idLocation",$arrayHelper)->get();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findByCountryAndCity(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'country' => 'required|string',
            'city' => 'required|string'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $arrayHelper = array();
        foreach(Location::where([["country","=",$request->country],["city","=",$request->city]])->get() as $val)
        {
            $arrayHelper[] = $val->idLocation;
        }
        $data = Listing::whereIn("idLocation",$arrayHelper)->get();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findByAllCriteria(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'country' => 'required|string',
            'city' => 'required|string',
            'keyword' => 'required|string',
            'idType' => 'required|numeric'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $arrayHelper = array();
        foreach(Location::where([["country","=",$request->country],["city","=",$request->city]])->get() as $val)
        {
            $arrayHelper[] = $val->idLocation;
        }
        $keyword = $request->keyword;
        $data = Listing::whereIn("idLocation",$arrayHelper)->where("idType","=",$request->idType)
        ->where(function($query) use ($keyword) {
            $query->where("description","LIKE",'%'.$keyword.'%')
            ->orWhere("title","LIKE",'%'.$keyword.'%');
        })->get();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findByAvailability(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'startTime' =>'required|date_format:Y-m-d H:i:s',
            'endTime' =>'required|date_format:Y-m-d H:i:s|after:startTime',
            'places' => 'required|numeric',
            'keyword' => 'required|string',
            'idType' => 'required|numeric'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $arrayHelper = array();
        $startDate = strtotime($request->startTime);
        $startDate = $this->days[date("D", $startDate)];
        $endDate = strtotime($request->endTime);
        $endDate = $this->days[date("D", $endDate)];
        foreach(WorkingTime::whereIn("day",[$startDate,$endDate])->get() as $val)
        {
            $startTimeExact = explode(" ",$val->startTime)[1];
            $endTimeExact = explode(" ",$val->endTime)[1];
            if((strtotime($startTimeExact)-strtotime(explode(" ",$request->startTime)[1]))>0)
            {
                continue;
            }
            if((strtotime($endTimeExact)-strtotime(explode(" ",$request->endTime)[1]))<0)
            {
                continue;
            }
            $arrayHelper[] = $val->idListing;
        }
        $arrayHelper2 = array();
        foreach(Product::where("seatsNumber",">=",$request->places)->get() as $val)
        {
            $arrayHelper2[] = $val->idListing;
        }
        $keyword = $request->keyword;
        $arrayWhere = array_intersect($arrayHelper,$arrayHelper2);
        $data = Listing::whereIn("idListing",$arrayWhere)->where("idType","=",$request->idType)
        ->where(function($query) use ($keyword) {
            $query->where("description","LIKE",'%'.$keyword.'%')
            ->orWhere("title","LIKE",'%'.$keyword.'%');
        })->get();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
    
    public function findOne($idListing)
    {
        $listing = Listing::find($idListing);
        if($listing==NULL)
            return response()->json([
                'erreur' => "Listing n'existe pas"
            ], 422);
        $listing->type = ListingType::find($listing->idType);
        $listing->location = Location::find($listing->idLocation);
        $listing->product = Product::where("idListing",$listing->idListing)->get();
        $listing->time = WorkingTime::where("idListing",$listing->idListing)->get();
        foreach($listing->time as $val)
        {
            $val->day = $this->dayss[$val->day];
        }
        $listing->photoUrl = url('storage/app/'.$listing->photo);
        return response()->json($listing);
    }
    
    public function findAll()
    {
        $data = Listing::all();
        foreach($data as $val)
        {
            $val->location = Location::find($val->idLocation);
            $val->photoUrl = url('storage/app/'.$val->photo);
            $val->lowPrice = Product::where('idListing', $val->idListing)->selectRaw('min(price) as price,currency')->groupBy("currency")->first();
        }
        return response()->json($data);
    }
}
