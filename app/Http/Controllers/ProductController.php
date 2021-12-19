<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Console\Input\Input;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filterInput = $request->all();

//        If filter initiate
        if (count($filterInput) > 0){

//            filter queries
            $query = $this->indexFilter($filterInput);
            $data['product'] = $query->with('productImages', 'productVariants', 'productVariantPrices')->get();
        }

//        without filter query
        else{
            $data['product'] = Product::with('productImages', 'productVariants', 'productVariantPrices')->get();
        }

        $data['sl'] = 1;

//        variant grp for filter
        $data['all_variant'] = $this->variantGrp();

        return view('products.index', $data);
    }

//            filter queries
    public function indexFilter($filterInput){
        $query = Product::query();

        if ($filterInput['title'] != null){
            $query->where('products.title','LIKE', '%'.$filterInput['title'].'%');
        }
        if ($filterInput['date'] != null){
            $query->whereDate('products.created_at','=', $filterInput['date']);
        }

        if ($filterInput['variant'] != null){
            $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->select('title', 'description', 'products.created_at', 'products.id')
                ->where('product_variants.variant','LIKE', '%'.$filterInput['variant'].'%')
                ->groupBy('products.id');
        }

        if ($filterInput['price_from'] != null || $filterInput['price_to'] != null){
            $query->leftJoin('product_variant_prices', 'products.id', '=', 'product_variant_prices.product_id')
                    ->select('title', 'description', 'products.created_at', 'products.id')->groupBy('products.id');
        }
        if ($filterInput['price_from'] != null){
            $query->where('product_variant_prices.price', '>=', $filterInput['price_from']);
        }

        if ($filterInput['price_to'] != null){
            $query->where('product_variant_prices.price', '<=', $filterInput['price_to']);
        }
        return $query;
    }

    //        variant grp for filter
    public function variantGrp(){
        $variantTypes = Variant::pluck('id', 'title')->toArray();
        foreach ($variantTypes as $key => $value){
            $variantGrp[$key] = ProductVariant::where('variant_id', $value)->select('variant')->groupBy('variant')->get()->toArray();
        }
        return $variantGrp;
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
        $product['title'] = $request->input('title');
        $product['sku'] = $request->input('sku');
        $product['description'] = $request->input('description');
        $product_info = Product::create($product);

        foreach ($request->input('product_variant') as $product_variant){
            foreach ($product_variant['tags'] as $tag){
                $tag_data['variant'] = $tag;
                $tag_data['variant_id'] = $product_variant['option'];
                $tag_data['product_id'] = $product_info->id;

                $product_tag_data = ProductVariant::create($tag_data);
            }
        }

        return response()->json('Product created!');
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
