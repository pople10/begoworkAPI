<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Storage;

class UserController extends Controller
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
        $data_validator = Validator::make($request->all(),
        [
            'title' => 'required|string|max:5',
            'lastname' => 'required|string',
            'firstname' =>'required|string',
            'email'=>'required|email'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $users = User::all()->where("email",$request->email);
        if(count($users)!=0)
            return response()->json([
                'erreur' => 'Email s\'existe déjà'
            ], 422);
        if(!$user = User::create($request->all()))
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        if($request->title==="Mr.")
            $user->photo = "photos/no-photo-men.png";
        else
            $user->photo = "photos/no-photo-women.png";
        if(!$user->save())
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        return response()->json($user->idUser);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'lastname' => 'required|string',
            'firstname' =>'required|string',
            'email'=>'required|email',
            'photoAttachement' => 'file|mimes:jpeg,jpg,png'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $acc = Auth::user();
        $user = User::find($acc->idUser);
        $user->lastname = $request->lastname;
        $user->firstname = $request->firstname;
        if(User::where("email",$request->email)->get()->count()!=0 and $user->email!=$request->email)
            return response()->json([
                'erreur' => "Email existe déjà"
            ], 422);
        $user->email = $request->email;
        if($request->photoAttachement)
        {
            if(strpos($user->photo, "no-photo") === false)
                Storage::delete($user->photo);
            $filename=pathinfo($request->photoAttachement->getClientOriginalName(),PATHINFO_FILENAME);
            $extention=pathinfo($request->photoAttachement->getClientOriginalName(),PATHINFO_EXTENSION);
            $path = md5(time(). $filename).".".$extention;
            $path = request()->photoAttachement->storeAs('photos', $path);
            $user->photo = $path;
        }
        if(!$user->save())
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        return response()->json([
                'done' => "Modifié avec succès"
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
    
    public function getProfile()
    {
        $acc = Auth::user();
        $user = User::find($acc->idUser);
        $user->photoUrl = url('storage/app/'.$user->photo);
        return response()->json($user);
    }
}
