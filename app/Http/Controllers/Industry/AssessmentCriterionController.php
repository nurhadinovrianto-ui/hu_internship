<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCriterion;
use Illuminate\Http\Request;

class AssessmentCriterionController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index()
    {
        $supervisor = $this->getSupervisor();
        $industryId = $supervisor?->industry_id;

        $criteria = AssessmentCriterion::getForIndustry($industryId);
        $isUsingDefault = $criteria->every(fn($c) => is_null($c->industry_id));

        return view('industry.assessment.criteria.index', compact('criteria', 'isUsingDefault', 'industryId'));
    }

    public function store(Request $request)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor || !$supervisor->industry_id) {
            return back()->with('error', 'Profil industri Anda belum terhubung.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1|max:100',
            'description' => 'nullable|string',
        ]);

        AssessmentCriterion::create([
            'name' => $validated['name'],
            'weight' => $validated['weight'],
            'description' => $validated['description'] ?? null,
            'assessor_type' => 'industry',
            'industry_id' => $supervisor->industry_id,
            'is_active' => true,
            'sort_order' => AssessmentCriterion::where('industry_id', $supervisor->industry_id)->count() + 1,
        ]);

        return back()->with('success', 'Kriteria penilaian perusahaan berhasil ditambahkan.');
    }

    public function update(Request $request, AssessmentCriterion $criterion)
    {
        $supervisor = $this->getSupervisor();
        if ($criterion->industry_id !== $supervisor?->industry_id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $criterion->update($validated);

        return back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    public function destroy(AssessmentCriterion $criterion)
    {
        $supervisor = $this->getSupervisor();
        if ($criterion->industry_id !== $supervisor?->industry_id) {
            abort(403, 'Akses ditolak.');
        }

        $criterion->delete();

        return back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }

    /**
     * Copy default criteria into custom criteria for this company to customize
     */
    public function customizeDefault()
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor || !$supervisor->industry_id) {
            return back()->with('error', 'Profil industri Anda belum terhubung.');
        }

        $defaults = AssessmentCriterion::where('assessor_type', 'industry')
            ->whereNull('industry_id')
            ->orderBy('sort_order')
            ->get();

        foreach ($defaults as $item) {
            AssessmentCriterion::create([
                'name' => $item->name,
                'weight' => $item->weight,
                'description' => $item->description,
                'assessor_type' => 'industry',
                'industry_id' => $supervisor->industry_id,
                'is_active' => true,
                'sort_order' => $item->sort_order,
            ]);
        }

        return back()->with('success', 'Kriteria default berhasil disalin ke perusahaan Anda. Sekarang Anda dapat menyesuaikan rubrik penilaian perusahaan.');
    }
}
