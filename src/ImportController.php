<?php

namespace Dws\Importify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Rap2hpoutre\FastExcel\Facades\FastExcel;

class ImportController extends Controller
{

    public function index()
    {
        return view('importify::add');
    }
    public function fetchFile(Request $request)
    {
        $action = 'Save';
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current', $tables);
        $this->validate($request, [
            'file' => 'required',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Generate a file name with extension
            $fileName = time() . '_excel_file_temp.' . $file->getClientOriginalExtension();
            // Save the file
            $file->storeAs('public/files/excel', $fileName);

            $collection = FastExcel::import($file)->toArray();
            $rows = array();
            foreach ($collection[0] as $row => $key) {
                $rows[] = $row;
            }
            $rowInFile = $rows;
            Session::flash('success_message', 'Please select the row accordingly !');
            return view('importify::process')->with(compact('rowInFile', 'action', 'tables', 'fileName'));
        } else {
            Session::flash('error_message', 'Something went wrong!');
            return back();
        }
    }
    public function getColumns($table, Request $request)
    {
        $columns = Schema::getColumnListing($table);
        $rowInFile = $request->rowInFile;
        $html = "";
        foreach ($columns as $col) {
            $html .= view('importify::repeat')->with(compact('col', 'rowInFile'))->render();
        }
        return response()->json(['html' => $html]);
    }
    public function process(Request $request)
    {
        $combinedData = array_combine($request->name, $request->value);
        $collection = FastExcel::import(public_path('storage/files/excel/' . $request->fileName))->toArray();
        foreach ($collection as $fileData => $fileDataKey) {
            $tmpData = [];
            foreach ($combinedData as $index => $key) {
                if ($key == "*")
                    $fileDataKey[$key] = null;
                else
                    $tmpData[$index] = $fileDataKey[$key];
            }
            $row = DB::table($request->table)->insert($tmpData);
        }
        if ($row) {
            unlink('storage/files/excel/' . $request->fileName);
            Session::flash('success_message', 'File inserted successfully.');
        } else {
            Session::flash('error_message', 'File insertion failed.');
        }
        return view('importify::add');
    }

}

