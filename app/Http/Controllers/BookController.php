<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Book::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'publisher' => 'nullable',
            'year' => 'nullable|integer',
            'isbn' => 'required|unique:books,isbn',
            'stock' => 'required|integer',
        ]);

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'stock' => $request->stock,
        ]);

        $qrPath = "qrcodes/" . $book->id . ".png";

        Storage::disk('public')->put(
            $qrPath,
            QrCode::format('png')->size(300)->generate("BOOK_ID".$book->id)
        );

        $book->update([
            'qr_code' => $qrPath
        ]);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $book->update($request->all());

        return response()->json([
            'meesage' => 'Buku berhasil diupdate',
            'data' => $book
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        if ($book->qr_code && Storage::disk('public')->exists($book->qr_code)) {
            Storage::disk('public')->delete($book->qr_code);
        }

        $book->delete();

        return response()->json(['meesage' => 'Buku berhasil dihapus']);
    }
}
