<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\GuestUploadSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GuestUploadController extends Controller
{
    /**
     * Initialize a fresh 5-minute guest upload session.
     */
    public function initSession(Request $request)
    {
        $guestName = $request->input('guest_name');
        $mobile = $request->input('mobile');
        $room = $request->input('room');
        $reserveId = $request->input('reserve_id');

        $session = GuestUploadSession::createSession($guestName, $mobile, $room, $reserveId);

        $uploadUrl = route('guest.mobile-upload', ['token' => $session->token]);
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' . urlencode($uploadUrl);

        return response()->json([
            'success' => true,
            'token' => $session->token,
            'upload_url' => $uploadUrl,
            'qr_image_url' => $qrImageUrl,
            'expires_at' => $session->expires_at->toIso8601String(),
            'expires_in_seconds' => $session->getRemainingSeconds(),
            'guest_name' => $session->guest_name,
            'mobile' => $session->mobile,
        ]);
    }

    /**
     * Check session status for real-time polling from the receptionist check-in modal.
     */
    public function checkStatus(Request $request, $token)
    {
        $session = GuestUploadSession::where('token', $token)->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Upload session not found.',
            ], 404);
        }

        if ($session->isExpired() && $session->status !== 'completed') {
            if ($session->status !== 'expired') {
                $session->status = 'expired';
                $session->save();
            }

            return response()->json([
                'success' => true,
                'status' => 'expired',
                'expired' => true,
                'documents' => [],
                'message' => 'This QR scan link has expired (5-minute security limit).',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $session->status,
            'expired' => false,
            'guest_name' => $session->guest_name,
            'mobile' => $session->mobile,
            'id_card_type' => $session->id_card_type,
            'id_card_number' => $session->id_card_number,
            'documents' => $session->documents ?? [],
            'documents_count' => is_array($session->documents) ? count($session->documents) : 0,
            'expires_in_seconds' => $session->getRemainingSeconds(),
        ]);
    }

    /**
     * Show mobile web page when guest/staff scans the QR code.
     */
    public function showMobileUpload($token)
    {
        $session = GuestUploadSession::where('token', $token)->first();

        if (!$session || ($session->isExpired() && $session->status !== 'completed')) {
            return view('frontoffice.guest_upload.mobile', [
                'session' => $session,
                'isExpired' => true,
                'isCompleted' => false,
                'expiresInSeconds' => 0,
            ]);
        }

        if ($session->status === 'completed') {
            return view('frontoffice.guest_upload.mobile', [
                'session' => $session,
                'isExpired' => false,
                'isCompleted' => true,
                'expiresInSeconds' => 0,
            ]);
        }

        $expiresInSeconds = $session->getRemainingSeconds();
        if ($expiresInSeconds <= 0) {
            return view('frontoffice.guest_upload.mobile', [
                'session' => $session,
                'isExpired' => true,
                'isCompleted' => false,
                'expiresInSeconds' => 0,
            ]);
        }

        return view('frontoffice.guest_upload.mobile', [
            'session' => $session,
            'isExpired' => false,
            'isCompleted' => false,
            'expiresInSeconds' => $expiresInSeconds,
        ]);
    }

    /**
     * Submit multiple images & documents uploaded from the mobile camera.
     */
    public function submitMobileUpload(Request $request, $token)
    {
        $session = GuestUploadSession::where('token', $token)->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found.'], 404);
        }

        if ($session->isExpired() && $session->status !== 'completed') {
            $session->status = 'expired';
            $session->save();
            return response()->json([
                'success' => false,
                'message' => 'This link has expired (5-minute security limit). Please request a new QR code at reception.'
            ], 410);
        }

        $uploadSubdir = 'uploads/guest_documents/' . date('Y/m');
        $uploadPath = public_path($uploadSubdir);
        File::ensureDirectoryExists($uploadPath, 0755, true);

        $existingDocs = is_array($session->documents) ? $session->documents : [];
        $newDocs = [];

        // 1. Process standard file uploads (supports multiple files under 'images' or 'documents')
        if ($request->hasFile('images')) {
            $docTypes = $request->input('document_types', []);
            foreach ($request->file('images') as $i => $file) {
                if ($file && $file->isValid()) {
                    $ext = $file->getClientOriginalExtension() ?: 'jpg';
                    $filename = 'doc_' . Str::random(16) . '_' . time() . '.' . $ext;
                    $file->move($uploadPath, $filename);
                    $webUrl = asset($uploadSubdir . '/' . $filename);
                    $docType = $docTypes[$i] ?? $request->input('document_type', 'ID Proof / Document');

                    $newDocs[] = [
                        'id' => (string) Str::uuid(),
                        'name' => $file->getClientOriginalName() ?: 'Document Image',
                        'type' => $docType,
                        'url' => $webUrl,
                        'uploaded_at' => now()->toIso8601String(),
                    ];
                }
            }
        }

        // 2. Process primary guest live selfie/photo if sent separately
        if ($request->hasFile('guest_photo')) {
            $file = $request->file('guest_photo');
            if ($file->isValid()) {
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = 'guest_photo_' . Str::random(16) . '_' . time() . '.' . $ext;
                $file->move($uploadPath, $filename);
                $newDocs[] = [
                    'id' => (string) Str::uuid(),
                    'name' => 'Guest Live Photo',
                    'type' => 'Guest Live Photo',
                    'url' => asset($uploadSubdir . '/' . $filename),
                    'uploaded_at' => now()->toIso8601String(),
                ];
            }
        }

        // 3. Process base64 data URLs (direct mobile camera snapshot from Canvas)
        $base64Images = $request->input('base64_images');
        if (is_array($base64Images)) {
            foreach ($base64Images as $imgItem) {
                $dataUrl = is_array($imgItem) ? ($imgItem['data'] ?? '') : $imgItem;
                $docType = is_array($imgItem) ? ($imgItem['type'] ?? 'Camera Photo') : 'Camera Photo';

                if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                    $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                    $data = base64_decode($data);
                    if ($data !== false) {
                        $ext = strtolower($type[1]);
                        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $ext = 'jpg';
                        }
                        $filename = 'cam_' . Str::random(16) . '_' . time() . '.' . $ext;
                        File::put($uploadPath . '/' . $filename, $data);

                        $newDocs[] = [
                            'id' => (string) Str::uuid(),
                            'name' => $docType,
                            'type' => $docType,
                            'url' => asset($uploadSubdir . '/' . $filename),
                            'uploaded_at' => now()->toIso8601String(),
                        ];
                    }
                }
            }
        }

        $allDocs = array_merge($existingDocs, $newDocs);

        if ($request->filled('id_card_type')) {
            $session->id_card_type = $request->input('id_card_type');
        }
        if ($request->filled('id_card_number')) {
            $session->id_card_number = $request->input('id_card_number');
        }

        $session->documents = $allDocs;
        $session->status = 'completed';
        $session->ip_address = $request->ip();
        $session->user_agent = $request->userAgent();
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Images and documents uploaded successfully!',
            'count' => count($allDocs),
            'documents' => $allDocs,
        ]);
    }
}
