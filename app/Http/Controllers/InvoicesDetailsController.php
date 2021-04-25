<?php

namespace App\Http\Controllers;

use App\Invoice_attachments;
use App\Invoices;
use App\InvoicesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InvoicesDetails  $invoicesDetails
     * @return \Illuminate\Http\Response
     */
    public function show(InvoicesDetails $invoicesDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InvoicesDetails  $invoicesDetails
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $invoices = Invoices::where('id',$id)->first();
        $details  = InvoicesDetails::where('id_Invoice',$id)->get();
        $attachments  = Invoice_attachments::where('invoice_id',$id)->get();

        return view('invoices.invoices_details',compact('invoices','details','attachments'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InvoicesDetails  $invoicesDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoicesDetails $invoicesDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InvoicesDetails  $invoicesDetails
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $invoice = Invoice_attachments::findOrFail($request->id_file);
        $invoice->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

    public function getFile($invoice_number, $file_name)
    {
        $content = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->download($content);
    }

    public function openFile($invoice_number, $file_name)
    {
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->file($files);
    }
}
