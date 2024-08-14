<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cpu;

class CpuController extends Controller
{
    public function index()
    {
        return response()->json(Cpu::all());
    }

    public function show($model)
    {
        $model = urldecode($model);
        $cpu = Cpu::where('model', $model)->first();

        if (!$cpu) {
            return response()->json(['error' => 'CPU not found'], 404);
        }

        return response()->json($cpu);
    }


    public function store(Request $request)
    {
        $cpu = new Cpu();
        $cpu->model = $request->input('model');
        $cpu->fabricante = $request->input('fabricante');
        $cpu->arquitetura = $request->input('arquitetura');
        $cpu->cores = $request->input('cores');
        $cpu->threads = $request->input('threads');
        $cpu->clock = $request->input('clock');
        $cpu->boost = $request->input('boost');
        $cpu->integrated_graphics = $request->input('integrated_graphics');
        $cpu->save();

        return response()->json($cpu, 201);
    }

    public function update(Request $request, $id)
    {
        $cpu = cpu::findOrFail($id);

        $cpu->model = $request->input('model', $cpu->model);
        $cpu->fabricante = $request->input('fabricante', $cpu->fabricante);
        $cpu->arquitetura = $request->input('arquitetura', $cpu->arquitetura);
        $cpu->cores = $request->input('cores', $cpu->cores);
        $cpu->threads = $request->input('threads', $cpu->threads);
        $cpu->clock = $request->input('clock', $cpu->clock);
        $cpu->boost = $request->input('boost', $cpu->boost);
        $cpu->integrated_graphics = $request->input('integrated_graphics', $cpu->integrated_graphics);

        $cpu->save();

        return response()->json($cpu, 200);
    }

    public function delete($id)
    {
        Cpu::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}