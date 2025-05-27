<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query();

        foreach ($request->query() as $key => $value) {
            if (in_array($key, (new Job())->getFillable())) {
                $query->where($key, $value);
            }
        }

        if ($request->has('order_by')) {
            $query->orderBy($request->get('order_by'), $request->get('order_dir', 'asc'));
        }

        $cacheKey = 'jobs_' . md5(json_encode($request->all()));
        $jobs = Cache::remember($cacheKey, 60, fn () =>
            $query->paginate($request->get('per_page', 20))
        );

        return JobResource::collection($jobs);
    }

    public function store(StoreJobRequest $request)
    {
        $job = Job::create($request->validated());

        Cache::flush();

        return new JobResource($job);
    }

    public function show(Job $job)
    {
        return new JobResource($job);
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        $job->update($request->validated());

        Cache::flush();

        return new JobResource($job);
    }

    public function destroy(Job $job)
    {
        $job->delete();

        Cache::flush();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function subscribe(Job $job, Candidate $candidate)
    {
        if ($job->paused) {
            return response()->json(['message' => 'Vaga pausada. Inscrição não permitida.'], 403);
        }

        $job->candidates()->syncWithoutDetaching($candidate->id);

        return response()->json(['message' => 'Inscrição realizada com sucesso.']);
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
