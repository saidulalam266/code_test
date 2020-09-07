@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <a class="collapse-item {{ url()->current() == route('product.index') ? 'active' : '' }}" href="{{ route('product.index') }}">All
                    </a>
                </div>
                <div class="col-md-2">
                    <input type="text" value="{{request('title')}}" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">
                            variant
                        </option>
                        @foreach($variants as $variant)
                        <option disabled>
                            {{ $variant->title }}
                        </option>
                        @foreach($variant->product_variants as $product_variant)
                        <option @if(request('variant')==$product_variant->variant) selected="" @endif value="{{$product_variant->variant}}">
                            {{ $product_variant->variant }}
                        </option>
                        @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" value="{{request('price_from')}}" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" value="{{request('price_to')}}" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" value="{{request('date')}}" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$product->title}}</td>
                        <td>{{$product->description}}</td>
                        <td>
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant@php echo $product->id @endphp">
                                @foreach($product->product_variant_prices as $variant_price)
                                <dt class="col-sm-3 pb-0">
                                    
                                    @if($variant_price->product_variant_ones)
                                    {{$variant_price->product_variant_ones->variant}}/ 
                                    @endif
                                    @if($variant_price->product_variant_twos)
                                    {{$variant_price->product_variant_twos->variant}}/
                                    @endif
                                     @if($variant_price->product_variant_threes)
                                    {{$variant_price->product_variant_threes->variant}}
                                    @endif
                                    
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($variant_price->price,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format($variant_price->stock,2) }}</dd>
                                    </dl>
                                </dd>
                                @endforeach
                            </dl>
                            <button onclick="$('#variant@php echo $product->id @endphp).toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of {{ $products->total() }} </p>
                </div>
                <div class="col-md-6">
                    <p>{{ $products->withQueryString()->links() }}</p>
                </div>
                
            </div>
        </div>
    </div>

@endsection
