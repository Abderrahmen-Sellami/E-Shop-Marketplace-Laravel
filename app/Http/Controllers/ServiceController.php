<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceController extends Controller
{
    use AuthorizesRequests;
    /**
     * Liste publique des services (Catalogue)
     */
    public function index()
    {
        // Utilisation de paginate pour éviter l'erreur "links() does not exist"
        $services = Service::where('is_active', true)->latest()->paginate(9);
        return view('services.index', compact('services'));
    }

    /**
     * Interface "Mes services" pour le vendeur connecté
     */
    public function manage()
    {
        
        $services = Auth::user()->services()->latest()->paginate(10);
        return view('services.manage', compact('services'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        // Vérifie si l'utilisateur a le droit de créer (via ServicePolicy)
        $this->authorize('create', Service::class);
        return view('services.create');
    }

    /**
     * Enregistrement du service
     */
    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()->route('services.manage')->with('success', 'Service ajouté avec succès.');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Service $service)
    {
        // Utilise la Policy pour vérifier si c'est bien le propriétaire
        $this->authorize('update', $service);
        
        return view('services.edit', compact('service'));
    }

    /**
     * Mise à jour du service
     */
    public function update(Request $request, Service $service)
    {
        // Sécurité via Policy
        $this->authorize('update', $service);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        $service->update($request->all());

        return redirect()->route('services.manage')->with('success', 'Service mis à jour.');
    }

    /**
     * Suppression du service
     */
    public function destroy(Service $service)
    {
        // Sécurité via Policy
        $this->authorize('delete', $service);

        $service->delete();
        return back()->with('success', 'Service supprimé.');
    }
}