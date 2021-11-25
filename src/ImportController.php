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
            'file' => 'required|mimes:xlsx,csv,txt',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFileName = $file->getClientOriginalName();
            $fileName = time() . '_excel_file_temp.' . $file->getClientOriginalExtension();
            $fileExtension = $file->getClientOriginalExtension();
            $file->storeAs('public/files/excel', $fileName);
            if ($fileExtension == "excel")
                $collection = FastExcel::import($file)->toArray();
            else
                $collection = FastExcel::configureCsv()->import(public_path('storage/files/excel/' . $fileName))->toArray();
            $rows = array();
            foreach ($collection[0] as $row => $key) {
                $rows[] = $row;
            }
            $rowInFile = $rows;
            Session::flash('success_message', 'Please select the row accordingly !');
            return view('importify::process')->with(compact('rowInFile', 'action', 'tables','fileName','fileExtension', 'originalFileName'));
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
        if ($request->fileExtension == "excel")
            $collection = FastExcel::import(public_path('storage/files/excel/' . $request->fileName))->toArray();
        else
            $collection = FastExcel::configureCsv()->import(public_path('storage/files/excel/' . $request->fileName))->toArray();
        foreach ($collection as $fileData => $fileDataKey) {
            $tmpData = [];
            foreach ($combinedData as $index => $key) {
                if ($key == "*")
                    $fileDataKey[$key] = null;
                else
                    $tmpData[$index] = $fileDataKey[$key];
            }
            $insertData[] = $tmpData;
        }
        for ($i = 0; $i <= count($insertData); $i++) {
            if ($i == count($insertData))
                break;
            try {
                test:
                $row = DB::table($request->table)->insert($insertData[$i]);
                $collection[$i]['Status'] = '1';
                $collection[$i]['Remarks'] = 'Row Inserted Successfully';
                $newData[] = $collection[$i];
                $flag = 1;
            } catch (\Exception $e) {
                $collection[$i]['Status'] = '0';
                $collection[$i]['Remarks'] = 'Row Insertion Failed';
                $newData[] = $collection[$i];
                $i++;
                goto test;
            }
        }
        $newData = collect($newData);
        FastExcel::data($collection)->export('public/exported/'.$request->originalFileName);
        $file = asset('storage/exported/'.$request->originalFileName);
        if ($flag) {
            unlink('storage/files/excel/' . $request->fileName);
            Session::flash('success_message', 'File inserted successfully.');
            Session::flash('download.in.the.next.request', $file);
        } else {
            Session::flash('error_message', 'File insertion failed.');
        }
        return view('importify::add');
    }
}
