<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportedDataAnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date,
            'average' => (float) $this->average,
            'median' => (float) $this->median,
            'min' => (float) $this->min,
            'max' => (float) $this->max,
            'above_10_percent' => (float) $this->above_10,
            'below_minus_10_percent' => (float) $this->below_minus_10,
            'between_minus10_and_10_percent' => (float) $this->between,
        ];
    }
}
