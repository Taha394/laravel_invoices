<?php

namespace App\Http\Controllers;

use App\Invoice_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachmentsController extends Controller
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file_name' => 'mimes:pdf,jpeg,png,jpg',
        ], [
            'file_name.mimes' => 'صيغه المرفق يجب ان تكون pdf , jpeg, png, jpg'
        ]);
        $image = $request->file('file_name');
        $file_name = $image->getClientOriginalName();
        $attachments = new Invoice_attachments();
        $attachments->file_name = $file_name;
        $attachments->invoice_number = $request->invoice_number;
        $attachments->invoice_id = $request->invoice_id;
        $attachments->created_by = Auth::user()->name;
        $attachments->save();
        //move pic
        $imageName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path('Attachment/' . date('Y') . $request->invoice_number), $imageName);
        session()->flash('Add', 'تم اضافه المرفق بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Invoice_attachments $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Invoice_attachments $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice_attachments $invoice_attachments)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Invoice_attachments $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Invoice_attachments $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice_attachments $invoice_attachments)
    {
        //
    }
}
