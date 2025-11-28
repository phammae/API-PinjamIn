<?php

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookObserver
{
    public function created(Book $book): void
    {
        $this->generateQrCode($book);
    }

    public function creating(Book $book)
    {
        $book->slug = Str::slug($book->title);
    }

    public function updating(Book $book)
    {
        $book->slug = Str::slug($book->title);
    }

    public function deleting(Book $book): void
    {
        if ($book->qr_code && Storage::disk('public')->exists($book->qr_code)) {
            Storage::disk('public')->delete($book->qr_code);
        }

        if ($book->covers && Storage::disk('public')->exists($book->covers)) {
            Storage::disk('public')->delete($book->covers);
        }
    }

    private function generateQrCode(Book $book): void
    {
        $qrPath = "qrcodes/" . $book->id . ".png";

        if (!Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->makeDirectory('qrcodes');
        }

        Storage::disk('public')->put(
            $qrPath,
            QrCode::format('png')->size(300)->generate("BOOK_ID" . $book->id)
        );

        $book->updateQuietly([
            'qr_code' => $qrPath
        ]);
    }
}
