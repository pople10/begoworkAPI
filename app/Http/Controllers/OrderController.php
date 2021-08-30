<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Statu;
use App\Models\Listing;
use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use DateTime;

use Notification;
use App\Notifications\MobileNotification;

class OrderController extends Controller
{
    
    public $days = [
        'Sun'=>'Sunday',
        'Mon'=>'Monday',
        'Tue'=>'Tuesday',
        'Wed'=>'Wednesday',
        'Thu'=>'Thursday',
        'Fri'=>'Friday',
        'Sat'=>'Saturday'
    ];
    
    public $Translator = [
        "Completed"=>"REMPLIE",
        "Pending"=>"EN ATTENTE",
        "Frozen"=>"BLOQUEE",
        "Refunded"=>"REMBOURSE"
    ];
    
    public $colors = [
        "Completed"=>"#30b74f",
        "Pending"=>"#ffc107",
        "Frozen"=>"#dc3545",
        "Refunded"=>"#17a2b8"
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
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
    
    public function findAllByAuth()
    {
        $data = Order::all()->where("idAccount",Auth::user()->idAccount)->sortByDesc("updated_at");
        $array = array();
        foreach($data as $val)
        {
            $val->statuDetail = Statu::find($val->idStatus);
            $val->statuDetail->bgColor = $this->colors[$val->statuDetail->label];
            $array[]=$val;
        }
        return response()->json($array);
    }
    
    public function findOneByAuth($ordID)
    {
        $ord = Order::find($ordID);
        if($ord==NULL)
            return response()->json([
                'erreur' => "Il n'y a pas de commande avec cet ID"
            ], 404);
        if($ord->idAccount!=Auth::user()->idAccount)
            return response()->json([
                'erreur' => "Vous n'avez pas le droit d'accès a cette commande"
            ], 422);
        $arrayHelper = array();
        foreach(ProductOrder::all()->where("idOrder",$ord->idOrder) as $val)
        {
            $prod = Product::find($val->idProd);
            $prod->title = Listing::find($prod->idListing)->title;
            $arrayHelper[] = $prod;
        }
        $ord->product = $arrayHelper;
        $ord->statuDetail = Statu::find($ord->idStatus);
        $ord->statuDetail->labelFr = $this->Translator[$ord->statuDetail->label];
        $ord->statuDetail->bgColor = $this->colors[$ord->statuDetail->label];
        return response()->json($ord);
    }
    
    public function reserve(Request $request)
    {
        if(Auth::user()->enabledPurchase!="1")
            return response()->json([
                'erreur' => 'Vous êtes bloqués de faire des reservations\nContacter notre support pour plus d\'informations'
            ], 422);
        $data_validator = Validator::make($request->all(),
        [
            'products' => 'required|string',
            'startTime' =>'required|date_format:Y-m-d H:i:s',
            'endTime' =>'required|date_format:Y-m-d H:i:s|after:startTime'
        ]);
        if ($data_validator->fails()) {
            return response()->json([
                'erreur' => $data_validator->errors()->first()
            ], 422);
        }
        $startingDate = DateTime::createFromFormat('Y-m-d H:i:s', $request->startTime)->format('Y-m-d');
        $endingDate = DateTime::createFromFormat('Y-m-d H:i:s', $request->endTime)->format('Y-m-d');
        if($startingDate != $endingDate) 
            return response()->json([
                'erreur' => "Les dates ne sont pas dans le même jour"
            ], 422);
        $prodsID = explode(",",$request->products);
        $allowedOrdersAux = Statu::all()->where("allowedToUse","1");
        $allowedOrders = array();
        foreach($allowedOrdersAux as $val)
        {
            $allowedOrders[]=$val->idStatus;
        }
        /*$startDate = strtotime($request->startTime);
        $startDate = $this->days[date("D", $startDate)];
        $endDate = strtotime($request->endTime);
        $endDate = $this->days[date("D", $endDate)];*/
        $dayName = strtotime($request->startTime);
        $dayName = $this->days[date("D", $dayName)];
        $dataTmp = DB::table("orders")->join('product_orders', 'orders.idOrder', '=', 'product_orders.idOrder')->whereIn("product_orders.idProd",$prodsID)->whereIn("orders.idStatus",$allowedOrders)->where([["orders.startTime",">=",$request->startTime],["orders.endTime","<=",$request->endTime]])->get();
        if(count($dataTmp)!=0)
            return response()->json([
                'erreur' => 'Déjà une reservation existe pour un des produits avec les dates séléctionner par vous'
            ], 422);
        $array = array_merge($request->all(), ['idStatus' => "2","idAccount" => Auth::user()->idAccount]);
        unset($array["products"]);
        $flag = "";
        DB::beginTransaction();
        if(!$ord = Order::create($array))
        {
            $flag="Erreur se produit dans la création du commande";
            
        }
        foreach($prodsID as $val)
        {
            $prodTmp = Product::find($val);
            if($prodTmp==NULL)
            {
                $flag="Un produit n'existe pas";
                break;
            }
            $listingTmp = Listing::find($prodTmp->idListing);
            if($listingTmp->availibility!="1")
            {
                $flag="Le bien ".$listingTmp->title." n'est pas disponible";
                break;
            }
            $workingTimeTmp = WorkingTime::where("idListing",$prodTmp->idListing)->where("day",$dayName)->first();
            if($workingTimeTmp==NULL)
            {
                $flag="Le bien ".$listingTmp->title." n'est pas disponible dans le temps choisi";
                break;
            }
            $startTimeExact = explode(" ",$workingTimeTmp->startTime)[1];
            $endTimeExact = explode(" ",$workingTimeTmp->endTime)[1];
            if((strtotime($startTimeExact)-strtotime(explode(" ",$request->startTime)[1]))>0)
            {
                $flag="Le bien ".$listingTmp->title." commence le ".$startTimeExact;
                break;
            }
            if((strtotime($endTimeExact)-strtotime(explode(" ",$request->endTime)[1]))<0)
            {
                $flag="Le bien ".$listingTmp->title." termine le ".$endTimeExact;
                break;
            }
            if(!ProductOrder::create(["idProd"=>$val,"idOrder"=>$ord->idOrder]))
            {
                $flag="Erreur se produit dans l'ajout d'un produit";
                break;
            }
        }
        if($flag!="")
        {
            DB::rollBack();
            return response()->json([
                'erreur' => $flag
            ], 422);
        }
        DB::commit();
        try{
            if(Auth::user()->enabledNotification)
                Auth::user()->notify(new MobileNotification("Nous avons reçu votre réservation","Réservation","high"));
        }
        catch(Exception $e)
        {
            
        }
        return response()->json([
                'done' => "Créé avec succès"
            ], 200);
    }
}
