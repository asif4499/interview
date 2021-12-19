@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{route('product.index')}}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input value="{{ Request::get('title') }}" type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">Select an Option</option>
                        @foreach($all_variant as $variant_key => $variant_value)
                            <optgroup label="{{$variant_key}}">
                                @foreach($variant_value as $variant_row)
                                    <option value="{{$variant_row['variant']}}" {{ Request::get('variant') == $variant_row['variant'] ? 'selected' : ''}}>{{$variant_row['variant']}}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input value="{{ Request::get('price_from') }}" type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input value="{{ Request::get('price_to') }}" type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input value="{{ Request::get('date') }}" type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary float-left"><i class="fa fa-search"></i></button>
                    <a href="{{route('product.index')}}" class="btn btn-secondary float-right">Clear</a>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table table-bordered" class="display nowrap" id="get-all-products">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($product as $row)
                        <tr>
                            <td>{{$sl++}}</td>
                            <td>{{$row->title}} <br> Created at : {{date_format(date_create($row->created_at),"d-M-Y")}}</td>
                            <td>{{$row->description}}</td>
                            <td>
                                @php
                                    $variantPrices = $row->productVariantPrices->toArray();
                                @endphp
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant{{'-'.$row->id}}">
                                    @foreach($variantPrices as $variantPrice)
                                        <dt class="col-sm-3 pb-0">
                                            {{productVariant($variantPrice)}}
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price : {{ number_format($variantPrice['price'],2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock : {{ number_format($variantPrice['stock'],2) }}</dd>
                                            </dl>
                                        </dd>
                                    @endforeach
                                </dl>
                                <button onclick="$('#variant{{'-'.$row->id}}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $row->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection


@push('scripts')
<script>
    import App from "../../../public/js/app";
    export default {
        components: {App}
    }
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
    {{--Datatable--}}
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js" type="text/javascript"></script>

    <script>
        $('#get-all-products').dataTable({
            processing: false,
            serverSide: false,
            searchDelay: 500,
        });
    </script>
@endpush
