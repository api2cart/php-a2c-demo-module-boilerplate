@if($product_id)
    {!! Form::open(['url' => route('products.update', [$store_id, $product_id]) ]) !!}
@else
@endif

<div class="alert alert-danger" role="alert" style="display: none;">
    <div id="_form_errors" class="text-left"></div>
</div>


<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-general" role="tab" aria-controls="nav-general" aria-selected="true">General</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-variant" role="tab" aria-controls="nav-variant" aria-selected="false">Variants</a>
        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-options" role="tab" aria-controls="nav-options" aria-selected="false">Options</a>
{{--        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-child" role="tab" aria-controls="nav-child" aria-selected="false">Child Items</a>--}}
    </div>
</nav>
<div class="tab-content" id="nav-tabContent" style="padding-top: 5px; text-align: left; color: white;">
    <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-home-tab">
        <div class="row">
            <div class="col-8">
                <div class="form-group row">
                    <label for="name" class="col-4 col-form-label">Name</label>
                    <div class="col-8">
                        <input type="text" class="form-control" id="name" name="name" value="{{ (isset($product['name'])) ? $product['name'] : '' }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                Variants Count: {{ (isset($product['children'])) ? count($product['children']) : '' }}
            </div>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="form-group row">
                    <label for="description" class="col-4 col-form-label">Description</label>
                    <div class="col-8">
                        <textarea class="form-control" rows="6" id="description" name="description">{!! (isset($product['description'])) ? $product['description'] : '' !!}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                Options Count: {{ (isset($product['product_options'][0]['option_items'])) ? count($product['product_options']) : '' }}
            </div>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="form-group row">
                    @if (!isset($product['children']))
                    <label for="price" class="col-4 col-form-label">Price</label>
                    <div class="col-8">
                        <input type="number" class="form-control" id="price" name="price" value="{{ ( isset($product['price']) ) ? $product['price'] : '' }}" step="0.01" @if( isset($product['children']) ) readonly @endif >
                        <div class="invalid-feedback"></div>
                    </div>
                    @else
                        <div class="col-12">
                            Price configured in <a href="#" onclick="$('#nav-profile-tab').click(); return false;">Variants</a> tab
                            <input type="hidden" id="price" name="price" value="{{ ( isset($product['price']) ) ? $product['price'] : '' }}"  readonly >
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-4">
{{--                Child Items Count:--}}
            </div>
        </div>

        <div class="row">

            <div class="col">
                <div class="form-group row">
                    <label for="images" class="col-2 col-form-label">Images</label>
                    <div class="col-10">
                        <input id="images" name="images[]" type="file" class="file" data-preview-file-type="text" multiple>
                    </div>
                </div>
            </div>
        </div>
        @if( !isset($product['children']) )
        <div class="row">
            <div class="col">
                <div class="form-group row">
                    <div class="col-2"></div>
                    <div class="col-10">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="allSelectedProducts" id="allSelectedProducts">
                            <label class="custom-control-label" for="allSelectedProducts">Apply changes to all selected products</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <br>

    </div>

    <div class="tab-pane fade" id="nav-variant" role="tabpanel" aria-labelledby="nav-profile-tab">
        @if( !isset($product['children']) )
            <br><br>
            <h5>This product do not have variants</h5>
            <br><br>
        @else
            @foreach($product['children'] as $k=>$item)
                <div class="card">
                    <div class="card-header">
                        {{ (isset($item['name'])) ? $item['name'] : '' }}
                        <input type="hidden"  id="children.id.{{ $k }}" name="children[id][{{ $k }}]" value="{{ ( isset($item['id']) ) ? $item['id'] : '' }}" >
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="price" class="col-4 col-form-label">Price</label>
                            <div class="col-8">
                                <input type="number" class="form-control" id="children.default_price.{{ $k }}" name="children[default_price][{{ $k }}]" value="{{ ( isset($item['default_price']) ) ? $item['default_price'] : '' }}" step="0.01"  >
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            @endforeach
        @endif
    </div>

    <div class="tab-pane fade" id="nav-options" role="tabpanel" aria-labelledby="nav-contact-tab">
        <br><br>
        <h5>Coming soon...</h5>
        <br><br>
    </div>

    <div class="tab-pane fade" id="nav-child" role="tabpanel" aria-labelledby="nav-contact-tab">
        <br><br>
        <h5>Coming soon...</h5>
        <br><br>
    </div>
</div>
{!! Form::close() !!}
