<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
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
                'username' => 'required|min:4|unique:users,username',
                'email' => 'required|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required'
            ]);

            $prosesData = User::create([
                'username' => $request->username, 
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role'=> $request->role
            ]);
            
            if ($prosesData) { // Memeriksa apakah $prosesData adalah instance model yang valid
                return ApiFormatter::sendResponse(200, 'success', $prosesData);
            } else {
                return ApiFormatter::sendResponse(400, 'bad_request', 'Gagal menambahkan data, silahkan coba lagi !');
            }
        } catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad_request', $err->getMessage());
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
            $data = User::where('id', $id)->first();
            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
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
        try {
            $this->validate($Request, [
                'username' => 'required|min:4|unique:users,username,' . $id,
                'password' => 'required|min:6',
                'role' => 'required'
            ]);
        if ($request->password){
        $checkProses = User::where('id', $id)->update([
            'username' => $Request->username,
            'email' => $Request->email,
            'password' => hash::make($Request->password),
            'role' => $Request->role
        ]);
    }else{
        $checkProses = User::where('id', $id)->update([
            'username' => $Request->username,
            'email' => $Request->email,
            'role' => $Request->role
        ]);
    }
            if ($checkProses) {
                $data = User::where('id', $id)->first();
    
                return ApiFormatter::sendResponse(200, 'success', $data);
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
            $checkproses = User::where('id', $id)->delete();

            if ($checkproses) {
                return
                    ApiFormatter::sendResponse(200, 'succes', 'berhasil hapus data User!');
            }
        } catch (\Exception $err) {
            return
                ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    public function trash()
    {
        try {
            $data = User::onlyTrashed()->get();

            return
                ApiFormatter::sendResponse(200, 'succes', $data);
        } catch (\Exception $err) {
            return
                ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkRestore = User::onlyTrashed()->where('id',$id)->restore();

            if ($checkRestore) {
                $data = User::where('id', $id)->first();
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $this->validate($request , [
                'email' => 'required',
                'password' => 'required',
            ]);
            $user = User::where('email' , $request->email)->first();

            if(!$user) {
                return ApiFormatter::SendResponse(400, false, 'login gagal ! ');
            }else{
                $isvalid= Hash::check($request->password,$user->password);

                if (!$isvalid){
                    return ApiFormatter::sendResponse(400,false,'login gagal pasword salah !');
                }else{
                    $generateToken = bin2hex(random_bytes(40));
                
                
                $user->update([
                    'token' => $generateToken
                ]);
                return ApiFormatter::sendResponse(200,'login berhasil' ,$user);
                }
            }
        } catch(\Exception $err) {
             return ApiFormatter::sendResponse(400, false,$err->getMessage());
        }

    }
    
    public function logout(Request $request)
    {
        try {
            $this->validate($request , [
                'email' => 'required',
                
            ]);
            $user = User::where('email' , $request->email)->first();

            if(!$user) {
                return ApiFormatter::SendResponse(400, 'logout gagal ! ');
            }else{
              if (!$user->token){
                    return ApiFormatter::sendResponse(400,'logout gagal pasword salah !');
                }else{
                    $logout = $user->update(['token' => null]);
                if($logout){        
                return ApiFormatter::sendResponse(200,'logout berhasil');
                }
            }
            }
        } catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }

    }
    public function permanentDelete($id)
    {
        try{
            $cekPermanentDelete = User::onlyTrashed()->where('id', $id)->forceDelete();

            if ($cekPermanentDelete) {
                return
                ApiFormatter::sendResponse(200, 'success','Berhasil menghapus data secara permanen' );
            }
        } catch (\Exception $err) {
            return
            ApiFormatter::sendResponse(400,'bad_request', $err->getMessage());
        }

    }

    public function _construct()
{
    $this->middleware('auth:api');
}
}
