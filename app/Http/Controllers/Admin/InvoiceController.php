<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement invoice creation
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Hóa đơn đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.invoices.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.invoices.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implement invoice update
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Hóa đơn đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implement invoice deletion
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Hóa đơn đã được xóa thành công!');
    }

    /**
     * Export invoice as PDF
     */
    public function exportPdf(string $id)
    {
        // TODO: Implement PDF export
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(string $id)
    {
        // TODO: Implement mark as paid
        return redirect()->back()
            ->with('success', 'Hóa đơn đã được đánh dấu là đã thanh toán!');
    }

    /**
     * Send invoice to customer
     */
    public function send(string $id)
    {
        // TODO: Implement invoice sending
        return redirect()->back()
            ->with('success', 'Hóa đơn đã được gửi thành công!');
    }
}
