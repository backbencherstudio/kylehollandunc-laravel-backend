<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Report\Report;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $reports = Report::with('order')->latest()->get();
        foreach ($reports as $report) {
            $report->report_file = $report->report_file ? Storage::url($report->report_file) : null;
        }
        return $this->sendResponse($reports, 'Reports retrieved successfully.');
    }

    public function show($id)
    {
        try {
            $report = Report::with('order')->findOrFail($id);
            return $this->sendResponse($report, 'Report retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve report.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $report = Report::findOrFail($id);
            $report->test_date = $request->date;
            $report->result_status = $request->result_status;
            $report->lot = $request->lot;
            if ($request->hasFile('report_file')) {
                $reportFile = $request->file('report_file');
                $path = Storage::put('reports', $reportFile);
                $report->report_file = $path;
            }
            $report->result_summary = $request->result_summary;
            $report->save();
            return $this->sendResponse($report, 'Report updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update report.', ['error' => $e->getMessage()]);
        }
    }
}
