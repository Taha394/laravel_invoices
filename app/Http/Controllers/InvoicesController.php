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
     * @return Response
     */
    public function index()
    {
        $invoices = Invoices::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
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
     * @param Invoices $invoices
     * @return Response
     */
    public function show(Invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Invoices $invoices
     * @return Application|Factory|Response|View
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
     * @param Invoices $invoices
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
     * @param Invoices $invoices
     * @return Application|RedirectResponse|Response|Redirector
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
