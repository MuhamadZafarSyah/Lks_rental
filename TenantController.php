<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->token) {
            return response()->json([
                'message' => 'forbidden'
            ], 403);
        }

        $user = User::where('role', 'Tenant')->with('tenant')->get();

        return response()->json([
            'message' => 'success get tenants',
            'data' => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if (Auth::user()->role === 'Tenant') {
            return response()->json([
                'message' => 'Invalid Role'
            ], 403);
        }

        if (!$request->token) {
            return response()->json([
                'message' => 'forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'password' => 'required|min:4',
            'no_KTP' => 'required|unique:tenants,no_KTP',
            'name' => 'required',
            'date_of_birth' => 'required|date',
            'email' => 'required|email',
            'phone' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = new User();
        $user->username = $request->username;
        $user->password = bcrypt($request->username);
        $user->role = 'Tenant';
        $user->save();

        $tenant = new Tenant();
        $tenant->id_user = $user->id;
        $tenant->no_KTP = $request->no_KTP;
        $tenant->name = $request->name;
        $tenant->date_of_birth = $request->date_of_birth;
        $tenant->email = $request->email;
        $tenant->phone = $request->phone;
        $tenant->description = $request->description;
        $tenant->save();

        return response()->json([
            'message' => 'create register success'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        if (Auth::user()->role === 'Tenants') {
            return response()->json([
                'message' => 'Invalid Role'
            ], 403);
        }


        if (!$request->token) {
            return response()->json([
                'massage' => 'forbidden'
            ], 403);
        }

        $tenant = Tenant::where('id_user', $id)->first();

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username,' . $id,
            'password' => 'required|min:4',
            'no_KTP' => 'required|unique:tenants,no_KTP,' . $tenant->id,
            'name' => 'required',
            'date_of_birth' => 'required|date',
            'email' => 'required|email',
            'phone' => 'required',
            'description' => 'required',

        ]);


        // RETURN JSON IF VALIDATION IS ERRORS

        if ($validator->fails()) {
            return response()->json([
                'massage' => 'Invalid field',
                'errors' => $validator->errors()
            ]);
        }

        // MAKE VARIABLE $USER USED IT FOR QUERY USER WHERE ID IS FROM PARAMETER $ID
        $user = User::where('role', 'Tenant')->with('tenant')->where('id', $id)->first();

        // UPDATE FIELD USER USE VARIABLE $USER (EXAMPLE: $USER->USERNAME = $REQ->USERNAME;)
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        // UPDATE FIELD FROM RELATION OF USERS (TENANT), EXAMPLE $USER->TENANT->NO_KTP = $REQ->NO_KTP;
        $user->tenant->no_KTP = $request->no_KTP;
        $user->tenant->name = $request->name;
        $user->tenant->date_of_birth = $request->date_of_birth;
        $user->tenant->email = $request->email;
        $user->tenant->phone = $request->phone;
        $user->tenant->description = $request->description;
        $user->save();
        $user->tenant->save();


        // LAST, U THROW RESPONSE JSON TO FRONTEND WITH MESSAGE "UPDATE TENANT SUCCESS" STATUS CODE 200

        return response()->json([
            'massage' => 'Update Tenant Success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        //
        if (Auth::user()->role === 'Tenants') {
            return response()->json([
                'message' => 'Invalid Role'
            ], 403);
        }

        // VALIDASI TOKEN
        if (!$request->token) {
            return response()->json([
                'massage' => 'forbidden'
            ], 403);
        }

        User::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Data berhasil di hapus',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


}
