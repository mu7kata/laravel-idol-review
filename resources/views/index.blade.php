@extends('layouts.app')

@section('css')
<link href="{{ asset('css/top.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="row justify-content-center">
    @foreach($reviews as $review)
    <div class="col-md-4">
        <div class="card mb50">
            <div class="card-body">
                <div class='image-wrapper'><img class='book-image' src="{{ asset('images/booklogo.png') }}"></div>
                
                <h3 class="h3 book-title">{{$review->title}}</h3>
                <p class='description'>
                    {{$review->body}}
                    レビュー本文
                </p>
                <a href=""class='btn btn-secondary detail-btn'>詳細を読む</a>
            </div>
        </div>
    </div>
     @endforeach
</div>
{{ $reviews->links() }}
@endsection