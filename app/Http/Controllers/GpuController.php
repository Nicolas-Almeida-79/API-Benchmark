<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gpu;

class GpuController extends Controller
{
    public function index()
    {
        return response()->json(Gpu::all());
    }

    public function show($model)
    {
        $model = urldecode($model);
        $gpu = Gpu::where('model', $model)->first();

        if (!$gpu) {
            return response()->json(['error' => 'GPU not found'], 404);
        }

        return response()->json($gpu);
    }


    public function store(Request $request)
    {
        $gpu = new Gpu();
        $gpu->model = $request->input('model');
        $gpu->fabricante = $request->input('fabricante');
        $gpu->arquitetura = $request->input('arquitetura');
        $gpu->cuda_cores = $request->input('cuda_cores');
        $gpu->base_clock = $request->input('base_clock');
        $gpu->boost_clock = $request->input('boost_clock');
        $gpu->memory = $request->input('memory');
        $gpu->save();

        return response()->json($gpu, 201);
    }

    public function update(Request $request, $id)
    {
        $gpu = Gpu::findOrFail($id);

        $gpu->model = $request->input('model', $gpu->model);
        $gpu->fabricante = $request->input('fabricante', $gpu->fabricante);
        $gpu->arquitetura = $request->input('arquitetura', $gpu->arquitetura);
        $gpu->cuda_cores = $request->input('cuda_cores', $gpu->cuda_cores);
        $gpu->base_clock = $request->input('base_clock', $gpu->base_clock);
        $gpu->boost_clock = $request->input('boost_clock', $gpu->boost_clock);
        $gpu->memory = $request->input('memory', $gpu->memory);

        $gpu->save();

        return response()->json($gpu, 200);
    }


    public function destroy($id)
    {
        Gpu::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}