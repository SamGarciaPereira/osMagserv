<?php

namespace App\Http\Controllers\anexo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anexo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AnexoController extends Controller{
    public function store(Request $request){

        $request->validate([
            'arquivo' => 'required|file|mimes:pdf,jpg,png,jpeg,xlsx,xls,csv|max:10240', 
            'model_id' => 'required|integer',
            'model_type' => 'required|string', 
            'is_confidencial' => 'nullable|boolean',
        ]);

        if ($request->hasFile('arquivo')) {
            $file = $request->file('arquivo');
            $originalName = $file->getClientOriginalName();
            
            $path = $file->store('anexos', 'public');

            Anexo::create([
                'nome_original' => $originalName,
                'caminho' => $path,
                'anexable_id' => $request->model_id,
                'anexable_type' => $request->model_type,
                'is_confidencial' => $request->has('is_confidencial'),
            ]);

            return back()->with('success', 'Arquivo anexado com sucesso!');
        }

        return back()->with('error', 'Erro ao enviar arquivo.');
    }

    public function destroy(Anexo $anexo){

        if ($anexo->is_confidencial && Auth::check() && Auth::user()->isSupervisor()) {
            abort(403, 'Acesso negado. Não tem permissão para excluir arquivos confidenciais.');
        }

        if (Storage::disk('public')->exists($anexo->caminho)) {
            Storage::disk('public')->delete($anexo->caminho);
        }

        $anexo->delete();

        return back()->with('success', 'Anexo removido com sucesso!');
    }
    
    public function download(Anexo $anexo){

        if ($anexo->is_confidencial && Auth::check() && Auth::user()->isSupervisor()) {
            abort(403, 'Acesso negado. Este arquivo é confidencial e restrito à administração.');
        }

        if (Storage::disk('public')->exists($anexo->caminho)) {
            return Storage::disk('public')->download($anexo->caminho, $anexo->nome_original);
        }
        return back()->with('error', 'Arquivo não encontrado.');
    }

    public function show(Request $request, Anexo $anexo, $filename){

        if ($anexo->is_confidencial && Auth::check() && Auth::user()->isSupervisor()) {
            abort(403, 'Acesso negado. Este arquivo é confidencial e restrito à administração.');
        }

        if (!Storage::disk('public')->exists($anexo->caminho)) {
            return back()->with('error', 'Arquivo não encontrado.');
        }

        $extension = strtolower(pathinfo($anexo->nome_original, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $isImage = in_array($extension, $imageExtensions);

        if ($isImage && !$request->query('raw')) {
            return view('anexo.show', [
                'anexo' => $anexo,
                'filename' => $filename
            ]);
        }
        
        return Storage::disk('public')->response($anexo->caminho, $filename, [
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}