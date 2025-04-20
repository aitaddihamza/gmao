<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePreventive;
use Illuminate\Http\Request;

class MaintenancePreventiveController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function index() 
    {
        $maintenancePreventives = MaintenancePreventive::with(['pieces', 'equipement', 'user'])->get();
        dd($maintenancePreventives);
        return view('maintenance_preventives.index', compact('maintenancePreventives'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        dd($data); // Inspectez les données envoyées par le formulaire
    
        // Force the piece_id to 1 for all pieces
        foreach ($data['pieces'] as &$piece) {
            $piece['piece_id'] = 1;
        }

        $maintenancePreventive = MaintenancePreventive::create($data);
        $maintenancePreventive->pieces()->sync($data['pieces']);

        return redirect()->route('maintenance-preventives.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenancePreventive $maintenancePreventive)
    {
        $data = $request->all();

        // Force the piece_id to 1 for all pieces
        foreach ($data['pieces'] as &$piece) {
            $piece['piece_id'] = 1;
        }

        $maintenancePreventive->update($data);
        $maintenancePreventive->pieces()->sync($data['pieces']);

        return redirect()->route('maintenance-preventives.index');
    }
}