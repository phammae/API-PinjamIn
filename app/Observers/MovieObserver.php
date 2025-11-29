<?php

namespace App\Observers;

use App\Models\Movie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MovieObserver
{
    public function created(Movie $movie): void
    {
        $this->generateQrCode($movie);
    }

    public function creating(Movie $movie)
    {
        $movie->slug = Str::slug($movie->title);
    }

    public function updating(Movie $movie)
    {
        $movie->slug = Str::slug($movie->title);
    }

    public function deleting(Movie $movie): void
    {
        if ($movie->qr_code && Storage::disk('public')->exists($movie->qr_code)) {
            Storage::disk('public')->delete($movie->qr_code);
        }

        if ($movie->covers && Storage::disk('public')->exists($movie->covers)) {
            Storage::disk('public')->delete($movie->covers);
        }
    }

    private function generateQrCode(Movie $movie): void
    {
        $qrPath = "qrcodes/" . $movie->id . ".png";

        if (!Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->makeDirectory('qrcodes');
        }

        Storage::disk('public')->put(
            $qrPath,
            QrCode::format('png')->size(300)->generate("MOVIE_ID" . $movie->id)
        );

        $movie->updateQuietly([
            'qr_code' => $qrPath
        ]);
    }
}
