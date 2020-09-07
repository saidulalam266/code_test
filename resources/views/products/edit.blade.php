@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div id="app">
        <edit-product :product="{{ $product }}" :variants="{{ $variants }}" :variant_arr="{{ json_encode($variant_arr) }}" :product_variant_pricess="{{ json_encode($product_variant_pricess) }}">Loading</edit-product>
    </div>
@endsection
