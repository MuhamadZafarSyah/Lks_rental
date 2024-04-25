<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
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


        $car = Car::all();
        return response()->json([
            'message' => 'success get car',
            'data' => $car
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
            'name' => 'required|min:4|',
            'nopol' => 'required|min:4|unique:cars,nopol',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'errors' => $validator->errors()
            ], 422);
        }


        $cars = new Car();
        $cars->name = $request->name;
        $cars->nopol = $request->nopol;
        $cars->status = 'Available';
        $cars->save();

        return response()->json([
            'massage' => 'Create cars success'
        ], 200);
    }

    public function update(Request $request, $id)
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
            'name' => 'required|min:4',
            'nopol' => 'required|unique:cars,nopol,' . $id,
            'status' => 'required|',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'massage' => 'Invalid field'
            ]);
        }

        $cars = Car::where('id', $id)->first();

        $cars->name = $request->name;
        $cars->nopol = $request->nopol;
        $cars->status = $request->status;
        $cars->save();


        return response()->json([
            'massage' => 'Cars Update Success'

        ], 200);
    }

    public function destroy(Request $request, string $id)
    {
        //
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

        Car::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Data berhasil di hapus',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        //
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


        $car = Car::where('id', $id)->first();
        return response()->json([
            'message' => 'success get car',
            'data' => $car
        ], 200);
    }
}
