<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardNumber;
use Illuminate\Http\Request;

class CardNumberController extends Controller
{
    public function index(Request $request)
    {
        $cardNumbers = CardNumber::with('card.pos', 'assignedUser')
            ->when($request->search, fn ($q, $s) => $q->where('number', 'like', "%{$s}%"))
            ->when($request->card_id,  fn ($q, $c) => $q->where('card_id', $c))
            ->when($request->activate !== null && $request->activate !== '',
                fn ($q) => $q->where('activate', $request->activate))
            ->when($request->status !== null && $request->status !== '',
                fn ($q) => $q->where('status', $request->status))
            ->when($request->sell !== null && $request->sell !== '',
                fn ($q) => $q->where('sell', $request->sell))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $cards = Card::orderBy('name_en')->get();

        return view('admin.card_numbers.index', compact('cardNumbers', 'cards'));
    }

    public function create()
    {
        $cards = Card::orderBy('name_en')->get();

        return view('admin.card_numbers.create', compact('cards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'card_id'  => 'required|exists:cards,id',
            'number'   => 'required|string|max:200|unique:card_numbers,number',
            'activate' => 'required|in:1,2',
            'status'   => 'required|in:1,2',
            'sell'     => 'required|in:1,2',
        ]);

        CardNumber::create($data);

        return redirect()->route('admin.card-numbers.index')
            ->with('success', 'Card number added successfully.');
    }

    public function edit(CardNumber $cardNumber)
    {
        $cards = Card::orderBy('name_en')->get();

        return view('admin.card_numbers.edit', compact('cardNumber', 'cards'));
    }

    public function update(Request $request, CardNumber $cardNumber)
    {
        $data = $request->validate([
            'card_id'  => 'required|exists:cards,id',
            'number'   => 'required|string|max:200|unique:card_numbers,number,' . $cardNumber->id,
            'activate' => 'required|in:1,2',
            'status'   => 'required|in:1,2',
            'sell'     => 'required|in:1,2',
        ]);

        $cardNumber->update($data);

        return redirect()->route('admin.card-numbers.index')
            ->with('success', 'Card number updated successfully.');
    }

    public function destroy(CardNumber $cardNumber)
    {
        $cardNumber->delete();

        return redirect()->route('admin.card-numbers.index')
            ->with('success', 'Card number deleted successfully.');
    }

    public function printView(Request $request)
    {
        $cardNumbers = CardNumber::with('card.pos', 'assignedUser')
            ->when($request->search,  fn ($q, $s) => $q->where('number', 'like', "%{$s}%"))
            ->when($request->card_id, fn ($q, $c) => $q->where('card_id', $c))
            ->when($request->activate !== null && $request->activate !== '',
                fn ($q) => $q->where('activate', $request->activate))
            ->when($request->status !== null && $request->status !== '',
                fn ($q) => $q->where('status', $request->status))
            ->when($request->sell !== null && $request->sell !== '',
                fn ($q) => $q->where('sell', $request->sell))
            ->orderBy('card_id')
            ->orderBy('id')
            ->get();

        $cards = Card::orderBy('name_en')->get();

        return view('admin.card_numbers.print', compact('cardNumbers', 'cards'));
    }

    public function bulkGenerate(Request $request)
    {
        $data = $request->validate([
            'card_id' => 'required|exists:cards,id',
            'count'   => 'required|integer|min:1|max:500',
            'prefix'  => 'nullable|string|max:20',
            'length'  => 'required|integer|min:8|max:32',
        ]);

        $generated = 0;
        $attempts  = 0;
        $prefix    = $data['prefix'] ?? '';
        $numLength = $data['length'] - strlen($prefix);

        while ($generated < $data['count'] && $attempts < $data['count'] * 5) {
            $number = $prefix . strtoupper(substr(bin2hex(random_bytes((int) ceil($numLength / 2))), 0, $numLength));

            if (! CardNumber::where('number', $number)->exists()) {
                CardNumber::create([
                    'card_id'  => $data['card_id'],
                    'number'   => $number,
                    'activate' => 1,
                    'status'   => 2,
                    'sell'     => 2,
                ]);
                $generated++;
            }
            $attempts++;
        }

        return redirect()->route('admin.card-numbers.index', ['card_id' => $data['card_id']])
            ->with('success', "Generated {$generated} card numbers successfully.");
    }
}
