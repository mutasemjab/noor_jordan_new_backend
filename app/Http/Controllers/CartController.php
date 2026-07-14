<?php

namespace App\Http\Controllers;

use App\Models\CardNumber;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $items   = $this->getCart();
        $courses = Course::with(['teacher', 'category'])
            ->whereIn('id', array_keys($items))
            ->where('is_published', true)
            ->get()
            ->keyBy('id');

        $subtotal = $courses->sum(fn ($c) => (float) $c->price);
        $discount = 0;
        $total    = $subtotal - $discount;

        return view('front.cart', compact('courses', 'items', 'subtotal', 'discount', 'total'));
    }

    public function add(Request $request, int $id)
    {
        $course = Course::where('id', $id)->where('is_published', true)->firstOrFail();

        $cart = session('cart', []);
        $cart[$course->id] = 1;
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('cart_added', $course->title);
    }

    public function remove(int $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return back()->with('cart_removed', true);
    }

    public function checkout()
    {
        if (! auth('student')->check()) {
            return redirect()->route('student.login');
        }

        $items = $this->getCart();
        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        $courses = Course::with(['teacher'])
            ->whereIn('id', array_keys($items))
            ->where('is_published', true)
            ->get();

        return view('front.checkout', compact('courses'));
    }

    public function activate(Request $request)
    {
        if (! auth('student')->check()) {
            return redirect()->route('student.login');
        }

        $request->validate([
            'card_number' => 'required|string|max:100',
        ]);

        $items = $this->getCart();
        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        $cardNumber = CardNumber::where('number', trim($request->card_number))
            ->where('activate', 1) // active
            ->where('status', 2)   // not used
            ->where('sell', 1)     // sold
            ->first();

        if (! $cardNumber) {
            return back()
                ->withInput()
                ->with('activation_error', app()->getLocale() === 'ar'
                    ? 'رقم الكارت غير صحيح أو تم استخدامه مسبقاً.'
                    : 'Invalid card number or already used.');
        }

        $student    = auth('student')->user();
        $courseIds  = array_keys($items);

        DB::transaction(function () use ($student, $courseIds, $cardNumber) {
            foreach ($courseIds as $courseId) {
                Enrollment::firstOrCreate(
                    ['student_id' => $student->id, 'course_id' => $courseId],
                    [
                        'enrolled_at'          => now(),
                        'is_active'            => true,
                        'is_completed'         => false,
                        'progress_percentage'  => 0,
                    ]
                );
            }

            $cardNumber->update([
                'status'           => 1,
                'assigned_user_id' => $student->id,
            ]);
        });

        session()->forget('cart');

        return redirect()->route('home')
            ->with('activation_success', app()->getLocale() === 'ar'
                ? 'تم تفعيل الدورات بنجاح! يمكنك البدء الآن.'
                : 'Courses activated successfully! You can start now.');
    }

    private function getCart(): array
    {
        return session('cart', []);
    }
}
