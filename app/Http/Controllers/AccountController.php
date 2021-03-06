<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
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
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'password' =>'required|string|min:8|confirmed'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $acc = Auth::user();
        $acc->password = Hash::make($request->password);
        if(!$acc->save())
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
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
    
    public function signUp(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'idUser' => 'required|numeric',
            'username' => 'required|string',
            'password' =>'required|string|min:8|confirmed',
            'idRole'=>'required|numeric',
            'idType'=>'required|numeric',
            'enabledNotification'=>'required|boolean',
            'enabledEmails'=>'required|boolean'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        if(Account::where([["idUser","=",$request->idUser],["idType","=",$request->idType]])->get()->count()!=0)
            return response()->json([
                'erreur' => "Compte déjà crée pour ce type"
            ], 422);
        if(Account::where("username",$request->username)->get()->count()!=0)
            return response()->json([
                'erreur' => "Nom d'utilisateur déjà existe"
            ], 422);
        $request->merge([
            'password' => Hash::make($request->password)
        ]);
        $array = array_merge($request->all(), ['vkey' => random_int(100000, 999999),"api_token"=>Str::random(60)]);
        unset($array["password_confirmation"]);
        if(!$acc = Account::create($array))
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        return response()->json([
                'done' => "Créé avec succès"
            ], 200);
    }
    
    public function login(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'username' => 'required|string',
            'password' =>'required|string'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $acc = Account::where([["username","=",$request->username]])->get();
        if($acc->count()==0 || !Hash::check($request->password, $acc[0]->password))
        {
            if(count($acc)>0)
            {
                $acc[0]->loginAttemps = $acc[0]->loginAttemps+1;
                $acc[0]->save();
            }
            return response()->json([
                'erreur' => "Données erronées"
            ], 422);
        }
        $acc = $acc[0];
        if((time() - strtotime($acc->updated_at))>3600 and $acc->loginAttemps!=0)
        {
            $acc->loginAttemps = 0;
            $acc->save();
        }
        if($acc->loginAttemps>=3)
            return response()->json([
                'erreur' => "Vous avez dépasser le maximum nombre d'essais pour se connecter.\nVeuillez essayer après une heure"
            ], 422);
        if($acc->isDeleted)
            return response()->json([
                'erreur' => "Ce compte est déja supprimé"
            ], 422);
        if(!$acc->enabledLogin)
            return response()->json([
                'erreur' => "Ce compte est désactivé"
            ], 422);
        if($acc->loginAttemps!=0)
        {
            $acc->loginAttemps = 0;
            $acc->save();
        }
        $token = $acc->createToken($acc->idUser." ".$acc->username);
        try{
            $response = Http::withToken($token->plainTextToken)->post(url('api/exponent/devices/subscribe'), [
                'expo_token' => $request->expo_token
            ]);
        }
        catch(Exception $e)
        {
            
        }
        return response()->json(["token"=>$token->plainTextToken]);
        
    }
    
    public function delete(Request $request)
    {
        Auth::user()->tokens()->delete();
        $acc = Auth::user();
        $acc->isDeleted = true;
        if(!$acc->save())
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        return response()->json([
                'done' => "Supprimée avec succès"
            ], 200);
    }
    
    public function logout(Request $request)
    {
        try{
            $response = Http::withToken($request->plainTextToken)->post(url('api/exponent/devices/unsubscribe'), [
                'expo_token' => $request->expo_token
            ]);
        }
        catch(Exception $e)
        {
            
        }
        $request->user()->currentAccessToken()->delete();
        return response()->json([
                'done' => "Se déconnecté avec succès"
            ], 200);
    }
    
    public function sendResetRequest(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'username' => 'required|string'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $acc = Account::where([["username","=",$request->username]])->get();
        if(count($acc)==0)
            return response()->json([
                'erreur' => 'Il n\'a pas du compte avec ce nom d\'utilisateur'
            ], 422);
        $emailController = new EmailController();
        $acc=$acc[0];
        $user = User::find($acc->idUser);
        if(!$emailController->sendMessage("Votre code pour la réinstialisation est : ".$acc->vkey."<br><p style='color:#f15036'>Ne communique ce code à n'importe qui!</p>","[Begowork] ".$acc->vkey." Code de reinstalisation",$user->email))
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        return response()->json([
                'done' => "Nous avons envoyé un mail à votre email.\nVeuillez recuperer un code."
            ], 200);
    }
    
    public function resetPassword(Request $request)
    {
        $data_validator = Validator::make($request->all(),
        [
            'username' => 'required|string',
            'code' => 'required|numeric',
            'password' => 'required|min:8|string|confirmed'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $acc = Account::where([["username","=",$request->username]])->get();
        if(count($acc)==0)
            return response()->json([
                'erreur' => 'Il n\'a pas du compte avec ce nom d\'utilisateur'
            ], 422);
        $acc=$acc[0];
        if(!$acc->enabledLogin)
            return response()->json([
                'erreur' => 'Votre compte est désactivé'
            ], 422);
        if($acc->isDeleted)
            return response()->json([
                'erreur' => 'Votre compte est supprimé'
            ], 422);
        if($acc->vkey != $request->code)
            return response()->json([
                'erreur' => 'Code incorrect'
            ], 422);
        $acc->password = Hash::make($request->password);
        $acc->vkey = random_int(100000, 999999);
        if(!$acc->save())
        {
            return response()->json([
                'erreur' => "Erreur se produit"
            ], 422);
        }
        
        $user = User::find($acc->idUser);
        $emailController = new EmailController();
        $emailController->sendMessage("Vous avez reinitialisé votre mot de passe avec succès. Si vous n'êtes pas le responsable, nous contactez.","Mot de passe changé ⚠️",$user->email);
            
        return response()->json([
                'done' => "Reinitalisé avec succès"
            ], 200);
    }
}
