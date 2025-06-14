@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Books</h5>
                    <div>
                        <a href="{{ route('books.archived') }}" class="btn btn-secondary me-2">View Archived Books</a>
                        <a href="{{ route('books.create') }}" class="btn btn-primary">Add New Book</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Book Code</th>
                                    <th>Name</th>
                                    <th>Author</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($books as $book)
                                    <tr>
                                        <td>{{ $book->book_code }}</td>
                                        <td>{{ $book->name }}</td>
                                        <td>{{ $book->author }}</td>
                                        <td>{{ $book->section }}</td>
                                        <td>
                                            @if($book->isBorrowed())
                                                <span class="badge bg-warning">Borrowed</span>
                                            @else
                                                <span class="badge bg-success">Available</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('books.edit', $book) }}" class="btn btn-primary btn-sm">Edit</a>
                                            @if(!$book->isBorrowed())
                                                <form action="{{ route('books.archive', $book) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to archive this book?')">
                                                        Archive
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No books found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 