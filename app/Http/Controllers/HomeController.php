<?php

namespace App\Http\Controllers;

use App\Entry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get entries
        if (!empty($request->start)) {
            $entries = Entry::where('owner', Auth::user()->id)
                ->whereBetween('created_at', [$request->start, $request->end])
                ->orderBy('id', 'DESC');
        } else {
            $entries = Entry::where('owner', Auth::user()->id)
                ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
                ->orderBy('id', 'DESC');
        }

        // Get entries
        $entries = $entries->get();

        return view('home', compact('entries'));
    }

    public function create(Request $request)
    {
        Entry::create([
            'owner' =>  Auth::user()->id,
            'amount'    =>  $request->amount,
            'description'   =>  $request->description,
            'type'  =>  $request->type
        ]);

        $request->session()->flash('success', 'Entry saved.');
        return redirect('/home');
    }

    public function edit(Request $request)
    {
        $entry = Entry::find($request->id);
        // Check if we own this
        if ($entry->owner != Auth::user()->id) {
            $request->session()->flash('error', 'You are not allowed to edit this entry.');
            return redirect('/home');
        }

        // Update
        $entry->update([
            'amount'    =>  $request->amount,
            'description'   =>  $request->description,
            'type'  =>  $request->type
        ]);

        // Return
        $request->session()->flash('success', 'Entry updated.');
        return redirect('/home');
    }

    public function password(Request $request)
    {
        $this->validate($request, [
            'password'  =>  'required|confirmed'
        ]);

        Auth::user()->password = bcrypt($request->password);
        Auth::user()->save();

        $request->session()->flash('success', 'Password updated.');
        return redirect('/home');
    }

    public function export(Request $request)
    {
        // Get entries
        if (!empty($request->start)) {
            $entries = Entry::where('owner', Auth::user()->id)
                ->whereBetween('created_at', [$request->start, $request->end])
                ->orderBy('id', 'DESC');
        } else {
            $entries = Entry::where('owner', Auth::user()->id)
                ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
                ->orderBy('id', 'DESC');
        }

        // Get entries
        $entries = $entries->get();

        // Start and End
        $start = Carbon::now()->subMonth()->format("F Y");
        $end = Carbon::now()->format("F Y");
        if (!empty($request->start)) {
            $start = Carbon::parse($request->start)->format("F j, Y");
            $end = Carbon::parse($request->end)->format("F j, Y");
        }

        // Stream PDF
        $pdf = PDF::loadView('pdf.report', compact('entries', 'start', 'end'));
        return $pdf->stream();
    }
}
