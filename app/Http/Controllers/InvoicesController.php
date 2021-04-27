<?php

namespace App\Http\Controllers;

use App\Invoice_attachments;
use App\Invoices;
use App\InvoicesDetails;
use App\Sections;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $invoices = Invoices::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $sections = Sections::all();
        return view('invoices.add_invoices', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        Invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_commission' => $request->Amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعه',
            'value_status' => 2,
            'note' => $request->note,
        ]);

        $id_invoices = Invoices::latest()->first()->id;
        InvoicesDetails::create([
            'id_Invoice' => $id_invoices,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'status' => 'غير مدفوعه',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {
//            $this->validate($request, ['pic' => 'required|mimes:pdf|max:1000'], ['pic.mimes' => 'wrong:saved']);
            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;
            $attachment = new Invoice_attachments();
            $attachment->file_name = $file_name;
            $attachment->invoice_number = $invoice_number;
            $attachment->created_by = Auth::user()->name;
            $attachment->invoice_id = $invoice_id;
            $attachment->save();

            //move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachment/' .date('Y'). $invoice_number), $imageName);

        }

        session()->flash('Add', 'تم اضافه الفاتوره بنجاح');
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Application|Factory|View
     */
    public function show($id)
    {
//        return $request;
        $invoices = Invoices::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    public function statusUpdate($id, Request $request)
    {
        $invoices = Invoices::findOrFail($id);
        if ($request->status === 'مدفوعة' )
        {
            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_date' =>$request->payment_date,
            ]);
            InvoicesDetails::create([
                'id_Invoice' =>$request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'payment_date' =>$request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }
        else
        {
            $invoices->update([
                'value_status' => 3,
                'status' => $request->status,
                'payment_date' =>$request->payment_date,
            ]);
            InvoicesDetails::create([
                'id_Invoice' =>$request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'payment_date' =>$request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('status_update');
        return redirect('/invoices');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $invoices = Invoices::where('id', $id)->first();
        $sections = Sections::all();
        return view('invoices.edit_invoice', compact('sections', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $invoices = Invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_commission' => $request->Amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        session()->flash('edit');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoices::where('id', $id)->first();
        $Details = Invoice_attachments::where('invoice_id', $id)->first();

        $id_page = $request->id_page;


        if (!$id_page == 2) {

            if (!empty($Details->invoice_number)) {

                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);

//                Storage::disk('public_uploads')->delete($Details->invoice_number.'/'. $Details->file_name);
            }

            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');

        } else {

            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/archive');
        }
    }

    public function getProducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }
}
