<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use Illuminate\Http\Request;
use App\Models\BookingServis;

class BookingServisController extends Controller
{
    public function index()
    {
        $bookings = BookingServis::all();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama_kendaraan' => 'required|string',
            'plat' => 'required|string',
            'keluhan' => 'required|string',
            'tgl_booking' => 'required|date',
            'jam_booking' => 'required|date_format:H:i',
            'jenis_layanan' => 'sometimes|required|array|min:1',
            'jenis_layanan.*.layanan' => 'required|string',
            'jenis_layanan.*.harga_layanan' => 'required|numeric|min:1',
            'status' => 'required|integer|in:0,1,2,3,4',
            'bengkel_id' => 'required|exists:bengkels,id',
        ]);

        $validated['nama'] = $user->name;
        $validated['no_hp'] = $user->no_hp;
        $validated['user_id'] = $user->id;

        $booking = BookingServis::create($validated);
        return response()->json([
            'message' => 'Booking created successfully',
            'bengkel' => $booking
        ], 201);
    }

    public function show($id)
    {
        $booking = BookingServis::findOrFail($id);
        $user = auth()->user();

        if ($booking->user_id === $user->id) {
            return response()->json($booking);
        }

        if ($booking->bengkel && $booking->bengkel->owner_id === $user->id) {
            return response()->json($booking->makeHidden('bengkel'));
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function update(Request $request, $id)
    {
        $booking = BookingServis::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'user') {
            if ($booking->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'nama_kendaraan' => 'sometimes|string',
                'plat' => 'sometimes|string',
                'keluhan' => 'sometimes|string',
                'tgl_ambil' => 'sometimes|date',
                'jam_ambil' => 'sometimes|date_format:H:i',
            ]);

            $booking->update($validated);
        } elseif ($user->role === 'admin_bengkel') {
            if ($booking->bengkel->owner_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'status' => 'sometimes|integer|in:0,1,2,3,4',
                'detail_servis' => 'sometimes|required|array|min:1',  // Validate it's an array
                'detail_servis.*.sparepart' => 'required_without:detail_servis.*.jasa|string',  // Validate sparepart if jasa is not present
                'detail_servis.*.harga_sparepart' => 'required_without:detail_servis.*.jasa|numeric|min:1',  // Validate harga_sparepart if jasa is not present
                'detail_servis.*.jasa' => 'required_without:detail_servis.*.sparepart|string',  // Validate jasa if sparepart is not present
                'detail_servis.*.harga_jasa' => 'required_without:detail_servis.*.sparepart|numeric|min:1',  // Validate harga_jasa if jasa is present
            ]);


            $booking->update($validated);

            if ($request->has('status') && $request->status == 2) {
                $sendNotificationController = app(SendNotification::class);

                $message = "Pemberitahuan: Kendaraan Anda telah selesai diservis.\n\n";
                $message .= "Nama Pemilik: " . $booking->nama . "\n";
                $message .= "Nama Kendaraan: " . $booking->nama_kendaraan . "\n";
                $message .= "Plat Nomor: " . $booking->plat . "\n";
                $message .= "Keluhan: " . $booking->keluhan . "\n";
                $message .= "Tanggal Booking: " . $booking->tgl_booking . "\n\n";
                $message .= "Silakan untuk melakukan konfirmasi pengambilan kendaraan melalui website kami.\n\n";

                $details = is_string($booking->detail_servis) ? json_decode($booking->detail_servis, true) : $booking->detail_servis;

                $totalHarga = 0;  // Initialize total price variable
                if ($details && is_array($details)) {
                    $message .= "Detail Servis:\n";
                    foreach ($details as $detail) {
                        if (isset($detail['sparepart'])) {
                            // Handle sparepart
                            $message .= "- Sparepart: " . $detail['sparepart'] . ", Harga: Rp " . number_format($detail['harga_sparepart'], 0, ',', '.') . "\n";
                            $totalHarga += $detail['harga_sparepart'];  // Add sparepart price to total
                        } elseif (isset($detail['jasa'])) {
                            // Handle jasa (service)
                            $message .= "- Jasa: " . $detail['jasa'] . ", Harga: Rp " . number_format($detail['harga_jasa'], 0, ',', '.') . "\n";
                            $totalHarga += $detail['harga_jasa'];  // Add jasa price to total
                        }
                    }
                    $message .= "\nTotal Harga: Rp " . number_format($totalHarga, 0, ',', '.') . "\n";
                }


                $target = $booking->no_hp;

                $sendNotificationController->sendWhatsappMessage(new Request([
                    'target' => $target,
                    'message' => $message,
                ]));
            }

        }

        return response()->json($booking->makeHidden('bengkel'));
    }

    public function destroy($id)
    {
        $booking = BookingServis::findOrFail($id);
        $user = auth()->user();

        if ($booking->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->delete();
        return response()->json(['message' => 'Booking deleted']);
    }

    public function getByUserId()
    {
        $userId = auth()->id();
        $bookings = BookingServis::where('user_id', $userId)->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found for this user.'], 404);
        }

        return response()->json($bookings);
    }

    public function getByOwnerId()
    {
        $userId = auth()->id();
        $bengkel = Bengkel::where('owner_id', $userId)->first();

        if (!$bengkel) {
            return response()->json(['message' => 'Bengkel not found for this owner.'], 404);
        }

        $bookings = BookingServis::where('bengkel_id', $bengkel->id)->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found for this bengkel.'], 404);
        }

        return response()->json($bookings);
    }
}
