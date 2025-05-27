<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with('jobs');

        foreach ($request->query() as $key => $value) {
            if (in_array($key, (new Candidate())->getFillable())) {
                $query->where($key, $value);
            }
        }

        if ($request->has('order_by')) {
            $query->orderBy($request->get('order_by'), $request->get('order_dir', 'asc'));
        }

        $cacheKey = 'candidates_' . md5(json_encode($request->all()));
        $candidates = Cache::remember($cacheKey, 60, fn () =>
            $query->paginate($request->get('per_page', 20))
        );

        return CandidateResource::collection($candidates);
    }

    public function store(StoreCandidateRequest $request)
    {
        $candidate = Candidate::create($request->validated());

        Cache::flush();

        return new CandidateResource($candidate);
    }

    public function show(Candidate $candidate)
    {
        $candidate->load('jobs');
        return new CandidateResource($candidate);
    }

    public function update(UpdateCandidateRequest $request, Candidate $candidate)
    {
        $candidate->update($request->validated());

        Cache::flush();

        return new CandidateResource($candidate);
    }

    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        Cache::flush();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function bulkDelete(Request $request)
    {
        if (empty($request->all())) {
            return response()->json([
                'message' => 'O corpo da requisição deve conter um JSON válido',
                'errors' => [
                    'json' => ['O JSON fornecido é inválido ou está vazio']
                ]
            ], 422);
        }

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:users,id',
        ]);

        $count = User::whereIn('id', $validated['ids'])->delete();
        
        Cache::flush();

        return response()->json([
            'message' => 'Usuários deletados com sucesso',
            'deleted_count' => $count,
            'deleted_ids' => $validated['ids']
        ]);
    }
}
