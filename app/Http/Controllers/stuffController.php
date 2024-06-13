<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Stuff;
use Illuminate\Http\Request;

class stuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data =stuff::with('stuffStock','InboudStuffs','Lendings')->get();


           return ApiFormatter::sendResponse(200,'succes',$data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
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
        try {
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required'
            ]);


            $data = Stuff::create([
                'name' => $request->name,
                'category' => $request->category,
            ]);
            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $data = Stuff::where('id', $id)->with('stuffStock','InboudStuffs','Lendings')->first();
            if (is_null($data)){
                return ApiFormatter::sendResponse(400, 'bad request','data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            } 

        }catch (\Exception $err){
              return ApiFormatter::sendResponse(400,'bad request', $err->getMessage());
        }
    }

    /**  q      11` 2A
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try {
            $this->validate($request , [
                'name' => 'required',
                'category' => 'required'
            ]);

            $checkProses = Stuff::where('id' , $id)->update([
                'name' => $request->name,
                'category' =>$request->category
            ]);

            if ($checkProses) {
                $data = Stuff::find($id);
                return ApiFormatter::sendResponse(200, 'succes' ,$data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request' , 'Gagal mengubah data!');
            }
 
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = stuff::where('id', $id)->delete();
            
            // if (!$checkProses->inboudStuffs || !$checkProses->stuffStock || !$checkProses->lendings){
            //     return ApiFormatter::sendResponse(400, 'bad reques', 'tidak dapat menghapus data stuff');

            // }else{
            //     $checkProsces->delete();
            //     return ApiFormatter::sendResponse(200, 'success', 'data berhasil di hapus');    
            // }

            return ApiFormatter::sendResponse(200, 'success', 'data berhasil di hapus');
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        
        }
    }


    public function trash()
    {
        try{
            $data= stuff::onlyTrashed()->get();
            return apiFormatter::sendResponse(200, 'succes', $data);
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }


    public function restore($id)
    {
        try{
            $checkProses = stuff::onlyTrashed()->where('id',$id)->restore();

            if($checkProses) {
                $data= Stuff::find($id);

                return ApiFormatter::sendResponse(200, 'succes', $data);
            }else {
                return ApiFormatter::sendResponse(400, 'bad request', 'gagal mengembalikan data!');
            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    public function deletePermanent($id)
    {
        try {
            $checkProses = stuff::onlyTrashed()->where('id',$id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'berhasil mengahapus permanent data stuff');
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        
        }
    }

    public function _construct()
{
    $this->middleware('auth:api');
}
}
