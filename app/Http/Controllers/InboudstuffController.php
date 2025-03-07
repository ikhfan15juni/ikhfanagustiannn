<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\InboudStuff;
use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;


class InboudstuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if($request->filter_id){
               $data = InboudStuff::where('stuff_id', $request->filter_id)->with('stuff','stuff.stuffStock')->get();
            }else{
                $data = InboudStuff::all();
            }
            return ApiFormatter::sendResponse(200, 'succes', $data);
           }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request',$err->getMessage());
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
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proof_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $checkStuff = stuff::where('id' , $request->stuff_id)->first();

            if(!$checkStuff){
                return ApiFormatter::sendResponse(400,false , 'data stuff does not exist');
                
            } else{
                if($request->hasFile('proof_file')) {
                    $proof = $request->file('proof_file');
                    $destinationPath = 'proof/';
                    $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                    $proof->move($destinationPath, $proofName);
                }
                $createStock = InboudStuff::create([
                    'stuff_id' => $request->stuff_id,
                    'total' => $request->total,
                    'date' => $request->date,
                    'proof_file' => $proofName,
                ]);
    
                if ($createStock){
                    $getStuff = Stuff::where('id', $request->stuff_id)->first();
                    $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
    
                    if (!$getStuffStock){
                        $updateStock = StuffStock::create([
                            'stuff_id' => $request->stuff_id,
                            'total_available' => $request->total,
                            'total_defec' => 0,
                        ]);
                    } else {
                        $updateStock = $getStuffStock->update([
                            'stuff_id' => $request->stuff_id,
                            'total_available' =>$getStuffStock['total_available'] + $request->total,
                            'total_defec' => $getStuffStock['total_defec'],
                        ]);
                    }
    
                    if ($updateStock) {
                        $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                        $stuff = [
                            'stuff' => $getStuff,
                            'InboudStuff' => $createStock,
                            'stuffStock' => $getStock
                        ];
    
                        return ApiFormatter::sendResponse(200, 'Successfully Create A Inbound Stuff Data', $stuff);
                    } else {
                        return ApiFormatter::sendResponse(400, 'Failed To Update A Stuff Stock Data');
                    }
                }

            }

           
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
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
        try {
            $getInboundStuff = InboundStuff::with('stuff', 'stuff.stuffStock')->find($id);

            if (!$getInboundStuff) {
                return ApiFormatter::sendResponse(404, 'Data Inbound Stuff Not Found');
            }else {
                return ApiFormatter::sendResponse(200, 'Succesfully Get A Inbound Stuff Data', $getInboundStuff);
            }
    }catch (\Exception $e) {
        return ApiFormatter::sendResponse(400, $e->getMessage());
    };
}

    /**
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
    public function update(Request $request, $id)
    {
        {
            try {
                $getInboundStuff = InboudStuff::find($id);
    
                if (!$getInboundStuff) {
                    return ApiFormatter::sendResponse(404, false, 'Data inbound Stuff Not Found');
                }else{
                    $this->validate($request, [
                        'stuff_id' => 'required',
                        'total' => 'required',
                        'date' => 'required',
                    ]);
    
                    if ($request->hasFile('proff_file')) {
                        $proof = $request->file('proff_file');
                        $destinationPath = 'proff/';
                        $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                        $proof->move($destinationPath, $proofName);
    
                        // unlink(base_path('public/proff/' . $getInboundStuff
                        // ['proff_file']));
                    }else {$proofName = $getInboundStuff['proff_file'];
                    }
    
                    $getStuff = Stuff::where('id', $getInboundStuff['stuff_id'])->first();
                    $getStuffStock = StuffStock::where('stuff_id', $getInboundStuff['stuff_id'])->first();
                    //request tidak berubah
    
                    $getCurrenStock = StuffStock::where('stuff_id', $request['stuff_id'])->first();
    
                    if ($getStuffStock['stuff_id'] == $request['stuff_id']) {
                        $updateStock = $getStuffStock->update([
                            'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'] + $request->total,
                        ]);
                    }else {
                        $updateStock = $getStuffStock->update([
                            'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'],
                        ]);
    
                        $updateStock = $getCurrenStock->update([
                            'total_available' => $getStuffStock['total_available'] + $request['total'],
                        ]);
                    }
                    $updateInbound = $getInboundStuff->update([
                        'stuff_id' => $request->stuff_id,
                        'total' => $request->total,
                        'date' => $request->date,
                        'proff_file' => $proofName,  
                    ]);
    
                    $getStock = StuffStock::where('stuff_id', $request['stuff_id'])->first();
                    $getInbound = InboudStuff::find($id)->with('stuff', 'stuffStock');
                    $getCurrentStuff = Stuff::where('id', $request['stuff_id'])->first();
    
                    $stuff = [
                        'stuff' => $getCurrentStuff,
                        'InboudStuff' => $getInbound,
                        'stuffStock' => $getStock,
                    ];
    
                    return ApiFormatter::sendResponse(200, true, 'Successfully Update A Inbound Stuff Data', $stuff);
                }
            } catch (\Exception $err) {
                return ApiFormatter::sendResponse(400, false, $err->getMessage());
            }
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
            $checkProses = InboudStuff::where('id', $id)->first();
    
            if ($checkProses) {
    
                $dataStock = StuffStock::where('stuff_id', $checkProses->stuff_id)->first();
                if ($dataStock->total_available < $checkProses ->total){
                    return ApiFormatter::sendResponse(400, 'bad request', 'total kurang dari total peminjaman');
                }else{
                    $stuffId = $checkProses->stuff_id;
                    $totalInbound = $checkProses->total;
                    $checkProses->delete();

                }
                
                if ($dataStock) {
                    $total_available = (int)$dataStock->total_available - (int)$totalInbound;
                    $minusTotalStock = $dataStock->update(['total_available' => $total_available]);
    
                    if ($minusTotalStock) {
                        $updateStufAndInbound = Stuff::where('id', $stuffId)->with('InboudStuffs', 'stuffStock')->first();
                        return ApiFormatter::sendResponse(200, 'success', $updateStufAndInbound);
                    }
                } else {
                    // Tangani jika data stok tidak ditemukan
                    return ApiFormatter::sendResponse(404, 'not found', 'Data stok stuff tidak ditemukan');
                }
            } else {
                // Tangani jika data InboudStuff tidak ditemukan
                return ApiFormatter::sendResponse(404, 'not found', 'Data InboudStuff tidak ditemukan');
            }
        } catch (\Exception $err) {
            // Tangani kesalahan
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    public function trash()
    {
        try{
            $data= InboudStuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }


    public function restore($id)
    {
        try {
            // Memulihkan data dari tabel 'inbound_stuffs'
            $checkProses = InboudStuff::onlyTrashed()->where('id', $id)->restore();
    
            if ($checkProses) {
                // Mendapatkan data yang dipulihkan
                $restoredData = InboudStuff::find($id);
    
                // Mengambil total dari data yang dipulihkan
                $totalRestored = $restoredData->total;
    
                // Mendapatkan stuff_id dari data yang dipulihkan
                $stuffId = $restoredData->stuff_id;
    
                // Memperbarui total_available di tabel 'stuff_stocks'
                $stuffStock = StuffStock::where('stuff_id', $stuffId)->first();
                
                if ($stuffStock) {
                    // Menambahkan total yang dipulihkan ke total_available
                    $stuffStock->total_available += $totalRestored;
    
                    // Menyimpan perubahan pada stuff_stocks
                    $stuffStock->save();
                }
    
                return ApiFormatter::sendResponse(200, 'success', $restoredData);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    public function deletePermanent($id)
    {
        try {
            $getInbound = InboudStuff::onlyTrashed()->where('id',$id)->first();

            unlink(base_path('public/proof/'.$getInbound->proof_file));
            // Menghapus data dari database
            $checkProses = InboudStuff::where('id', $id)->forceDelete();
    
            // Memberikan respons sukses
            return ApiFormatter::sendResponse(200, 'success', 'Data inbound-stuff berhasil dihapus permanen');
        } catch(\Exception $err) {
            // Memberikan respons error jika terjadi kesalahan
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    private function deleteAssociatedFile(InboudStuff $InboudStuff)
    {
        // Mendapatkan jalur lengkap ke direktori public
        $publicPath = $_SERVER['DOCUMENT_ROOT'] . '/public/proof';

    
        // Menggabungkan jalur file dengan jalur direktori public
         $filePath = public_path('proof/'.$InboudStuff->proof_file);
    
        // Periksa apakah file ada
        if (file_exists($filePath)) {
            // Hapus file jika ada
            unlink(base_path($filePath));
        }
    }

    public function _construct()
{
    $this->middleware('auth:api');
}
    
}
