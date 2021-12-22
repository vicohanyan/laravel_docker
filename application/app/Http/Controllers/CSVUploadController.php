<?php

namespace App\Http\Controllers;

use App\Jobs\CSVFileImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CSVUploadController extends Controller
{
    public function createForm()
    {
        return view('csv/csv-upload');
    }

    public function fileUpload(Request $req)
    {
        $req->validate([
            'file' => 'required|mimes:csv,txt|max:16384'
        ]);

        if ($req->file()) {
            $fileName = time() . '_' . $req->file->getClientOriginalName();
            $req->file('file')->storeAs('uploads', $fileName, 'public');
            // Run CSV import Job
            $this->dispatch(new CSVFileImportJob(Storage::url($fileName)));
            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
        return back()->with('success', 'File upload failed.');
    }

}
