<?php

namespace App\Http\Controllers;

use App\Jobs\CSVFileImportJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CSVUploadController extends Controller
{

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
            $request->file('file')->storeAs('uploads', $fileName, 'public');
            // Run CSV import Job
            $this->dispatch(new CSVFileImportJob($request->file));
            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
        return back()->with('success', 'File upload failed.');
    }

}
