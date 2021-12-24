<?php

namespace App\Http\Controllers;

use App\Services\CSVImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CSVUploadController extends Controller
{

    private CSVImportService $CSVImportService;

    /**
     * @param CSVImportService $CSVImportService
     */
    public function __construct(CSVImportService $CSVImportService)
    {
        $this->CSVImportService = $CSVImportService;
    }

    public function createForm()
    {
        return view('csv/csv-upload');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function fileUpload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:16384'
        ]);

        if ($request->file()) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName);
            // Run CSV import
            $this->CSVImportService->import(Storage::path($filePath));
            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
        return back()->with('success', 'File upload failed.');
    }

}
