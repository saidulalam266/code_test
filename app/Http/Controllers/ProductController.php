<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        //dd();
        $products   = Product::query();
        if(request('title'))
        {
            $products->where('title', 'LIKE', '%'. request('title') .'%');
        }
        if(request('variant'))
        {
            $variant = request('variant');
            $products->whereHas('product_variants', function ($query) use($variant) {
                       $query->where('variant', $variant);
                 });
        }
        if(request('price_from') && request('price_to'))
        {
            $from = request('price_from');
            $to = request('price_to');
            $products->whereHas('product_variant_prices', function ($query) use($from,$to) {
                       $query->whereBetween('price', [$from,$to]);
                 });
        }
        if(request('date'))
        {
            $products->where('created_at', 'LIKE', request('date') .'%');
        }
        $products=$products->paginate(5);
        $variants=Variant::all();
        //dd($products);
        return view('products.index')->with(['products'=>$products,'variants'=>$variants]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        foreach ($request->product_variant as $option_key => $option) {
            foreach ($option['tags'] as $key => $value) {
                $arr[]=[
                    'variant_id'=>$option['option'],
                    'variant'=>$value
                ];
            }   
        }
        $product = new Product();
        $product->title =$request->title ;
        $product->sku =$request->sku ;
        $product->description =$request->description ;
        if($product->save()){
            $product_images = ProductImage::whereNull('product_id')->get();
            foreach ($product_images as $key => $value) {
                ProductImage::findORFail($value->id)->update(['product_id'=>$product->id]);
            }
            $product->variants()->attach($arr);
            foreach ($request->product_variant_prices as $variant_price_key => $variant_price) {
                $product_variant_one=NULL;
                $product_variant_two=NULL;
                $product_variant_three=NULL; 
                foreach ($product->product_variants as $key => $product_variant) {

                    foreach (explode('/', $variant_price['title']) as $key => $value) {
                            
                        if ($value == $product_variant->variant) {
                            switch ($key) {
                                case '0':
                                    $product_variant_one=$product_variant->id;
                                    break;
                                case '1':
                                    $product_variant_two=$product_variant->id;
                                    break;
                                case '2':
                                    $product_variant_three=$product_variant->id;
                                    break;
                            }
                        }
                    }
                    
                }

                $pvp[]=[
                        'product_variant_one'=>$product_variant_one,
                        'product_variant_two'=>$product_variant_two,
                        'product_variant_three'=>$product_variant_three,
                        'price'=>$variant_price['price'],
                        'stock'=>$variant_price['stock'],
                    ];
                //dd($pvp);
            }
            //dd($pvp);
            if (isset($pvp)) {
                $product->product_variant_prices()->createMany($pvp);
            }
            

        }

        return response()->json($product);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product=Product::with(['variants','product_variants','product_variant_prices'])->findOrFail($id);
        $variants = $product->variants->unique();
        //dd($variants[0]);
        $variant_arr=[];
        foreach ($variants as $key => $value) {
            $tags=[];
            foreach ($value->product_variants as $key => $product_variant) {
                $variant = ProductVariant::where(['product_id'=>$product->id,'id'=>$product_variant->id])->first();
                if ($variant) {
                    $tags[] = $variant->variant;
                }
                
            }
            $variant_arr[]=(object)[
                'option' => $value->id,
                'tags'   => $tags
            ];
        }
        $product_variant_prices=[];
        foreach ($product->product_variant_prices as $key => $value) {
            $product_variant_ones=$value->product_variant_ones->variant;
            $product_variant_ones = '';
            $product_variant_twos = '';
            $product_variant_threes = '';
            if ($value->product_variant_ones) {
                $product_variant_ones = $value->product_variant_ones->variant;
            }
            if ($value->product_variant_twos) {
                $product_variant_twos = $value->product_variant_twos->variant;
            }
            if ($value->product_variant_threes) {
                $product_variant_threes = $value->product_variant_threes->variant;
            }
            $product_variant_prices[]=(object)[
                    'title' => $product_variant_ones.'/'.$product_variant_twos.'/'.$product_variant_threes,
                    'price' => $value->price,
                    'stock' => $value->stock
            ];
            //dd();
        }
        //dd($product_variant_prices);
        $variants = Variant::all();
        return view('products.edit')->with(['product'=>$product,'variants'=>$variants,'variant_arr'=>$variant_arr,'product_variant_pricess'=>$product_variant_prices]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        foreach ($request->product_variant as $option_key => $option) {
            foreach ($option['tags'] as $key => $value) {
                $arr[]=[
                    'variant_id'=>$option['option'],
                    'variant'=>$value
                ];
            }   
        }
        $product = Product::findOrFail($id);
        $product->title =$request->title ;
        $product->sku =$request->sku ;
        $product->description =$request->description ;
        if($product->save()){
            $product_images = ProductImage::whereNull('product_id')->get();
            foreach ($product_images as $key => $value) {
                ProductImage::findORFail($value->id)->update(['product_id'=>$product->id]);
            }
            $product->variants()->detach();
            $product->variants()->attach($arr);
            foreach ($request->product_variant_prices as $variant_price_key => $variant_price) {
                $product_variant_one=NULL;
                $product_variant_two=NULL;
                $product_variant_three=NULL; 
                foreach ($product->product_variants as $key => $product_variant) {

                    foreach (explode('/', $variant_price['title']) as $key => $value) {
                            
                        if ($value == $product_variant->variant) {
                            switch ($key) {
                                case '0':
                                    $product_variant_one=$product_variant->id;
                                    break;
                                case '1':
                                    $product_variant_two=$product_variant->id;
                                    break;
                                case '2':
                                    $product_variant_three=$product_variant->id;
                                    break;
                            }
                        }
                    }
                    
                }
                $pvp[]=[
                        'product_variant_one'=>$product_variant_one,
                        'product_variant_two'=>$product_variant_two,
                        'product_variant_three'=>$product_variant_three,
                        'price'=>$variant_price['price'],
                        'stock'=>$variant_price['stock'],
                    ];
            }
            if (isset($pvp)) {
                //dd($pvp);
                $product->product_variant_prices()->delete();
                $product->product_variant_prices()->createMany($pvp);
            }

        }

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function image_upload(Request $request)
    {
        if ($request->file) {
            $imageName = time().'.'.$request->file->getClientOriginalExtension();
            ProductImage::create(['file_path'=>'public/images/'.$imageName]);
            $request->file->move(public_path('images'), $imageName);
              
            return response()->json(['success'=>'You have successfully upload file.']);
        }
    }
}
