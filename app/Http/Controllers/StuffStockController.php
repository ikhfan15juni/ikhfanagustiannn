<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\StuffStock;
use App\Models\Stuff;
use Illuminate\Http\Request;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    
    {
        try{
            $getStuffStock = StuffStock::with('stuff')->get();
            return Apiformatter::sendResponse(200,'succes get all stuff stock data' , $getStuffStock);
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
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
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function show(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function edit(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StuffStock $stuffStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(StuffStock $stuffStock)
    {
        //
    }

    public function addStock(Request $request ,$id)
    {
        try {
            $getStuffStock = StuffStock::find($id);
            
        if (!$getStuffStock){
            return ApiFormatter::sendResponse(404, 'data stuff stock not found');
        } else {
          $this->validate($request , [
            'total_available' => 'required',
            'total_defac' => 'required',
          ]);
          $addStock = $getStuffStock -> update([
            'total_available' => $getStuffStock['total_available'] + $request->total_available,
            'total_defac' => $getStuffStock['total_defac'] + $request->total_defac,
          ]);

          if ($addStock){
            $getStuffStock = StuffStock::where('id', $id) -> with('stuff')->first();

            return ApiFormatter::sendResponse(200,'succes add a stock' , $getStuffStock);
          }
        }
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }

        
    }
    public function substock(Request $request ,$id)
    {
        try {
            $getStuffStock = StuffStock::find($id);
            
        if (!$getStuffStock){
            return ApiFormatter::sendResponse(404, 'data stuff stock not found');
        } else {
          $this->validate($request , [
            'total_available' => 'required',
            'total_defac' => 'required',
          ]);
           $isStockAvailable = $getStuffStock ['total_available'] - $request -> total_available;
           $isStockDefac = $getStuffStock ['total_defac'] - $request -> total_defac;

           if ($isStockAvailable < 0 || $isStockDefac < 0){
            return ApiFormatter::sendResponse(400, ' a substtraction Stock cant less than a stock stored');
           }else{
            $subStock = $getStuffStock->update([
                'total_available' => $isStockAvailable,
                'total_defac' => $isStockDefac,
            ]);
            
            if($subStock){
                $getStockSub = StuffStock::where('id', $id) -> with('stuff') ->first();

                return ApiFormatter::sendResponse(200, 'succes sub a stock of stuff stock data' , $getStockSub);
            }
           }
        }
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    public function _construct()
{
    $this->middleware('auth:api');
}

}

 