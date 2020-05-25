@extends('layouts.app')

@section('script')
    <script type="text/javascript">

        let customers   = [];
        let subscribers = [];



        function loadData(){

            items = [];

            return axios({
                method: 'post',
                url: '{{ route('stores.list') }}',
                data: {
                    length: 10,
                    start: 0
                }
            }).then(function (response) {

                stores = response.data.data;

                if ( response.data.log ){
                    for (let k=0; k<response.data.log.length; k++){
                        logItems.push( response.data.log[k] );
                    }
                    calculateLog();
                }

                if ( stores.length == 0 ){
                    Swal.fire(
                        'Error!',
                        'Do not have store info, please check API log.',
                        'error'
                    );
                    $.unblockUI();
                    return;
                }

                for (let i=0; i<stores.length; i++){

                    blockUiStyled('<h3>Loading '+ stores[i].url +' information.</h3>');

                    axios({
                        method: 'post',
                        url: '{{ route('products.list') }}/'+stores[i].store_key,
                        data: {
                            length: 10,
                            start: 0
                        }
                    }).then(function (rep) {

                        let orders = rep.data.data;

                        blockUiStyled('<h3>Adding '+ stores[i].url +' product.</h3>');

                        for (let j=0; j<orders.length; j++){
                            orders[j].cart_id = stores[i];
                            items.push( orders[j] );
                        }


                        //update log count
                        if ( rep.data.log ){
                            for (let k=0; k<rep.data.log.length; k++){
                                logItems.push( rep.data.log[k] );
                            }
                            calculateLog();
                        }


                        var datatable = $( '#pdtable' ).dataTable().api();

                        datatable.clear();
                        datatable.rows.add( items );
                        datatable.draw();

                        $.unblockUI();

                        $.growlUI('Notification', stores[i].url + ' data loaded successfull!' , 500);
                        $('[data-toggle="popover"]').popover({
                            html: true
                        });

                        reinitActions();

                        // initFilters();

                    });


                   // load rest datatables
                    axios.all([ loadCustomers( stores[i].store_key ), loadSubscribers( stores[i].store_key )  ])
                        .then(axios.spread(function ( it1, it2 ) {

                            console.log( it1 );
                            console.log( it2 );

                        }));

                }




            }).catch(function (error) {
                // handle error
                // console.log(error.response);

                if ( error.response.data.log ){
                    for (let k=0; k<error.response.data.log.length; k++){
                        logItems.push( error.response.data.log[k] );
                    }
                    calculateLog();
                }

                $.unblockUI();

                Swal.fire(
                    'Error!',
                    'Do not have store info, please check API log.',
                    'error'
                )

            });
        }

        function loadCustomers(store_key)
        {
            return axios.post( '/customers/list/' + store_key );
        }

        function loadProducts(store_key)
        {
            return axios.post( '/products/list/' + store_key );
        }

        function loadSubscribers(store_key)
        {
            return axios.post( '/subscribers/list/' + store_key );
        }


        var table;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            blockUiStyled('<h3>Loading stores information.</h3>');

            loadData();

            table = $('#pdtable').DataTable( {
                processing: true,
                serverSide: false,
                // ordering: false,
                data: items,
                dom: '<"row"<"col"bl><"col"><"col">><t><"row"<"col"i><"col">>',
                buttons: [
                    {
                        text: 'Reload',
                        action: function ( e, dt, node, config ) {

                            window.location.reload();

                        }
                    }
                ],
                language: {
                    emptyTable: "Data loading or not available in table"
                },
                initComplete: function () {

                    $('#pdtable_filter input').focus();

                },
                columnDefs: [
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectAll': true,
                            'selectRow': true,
                            'selectAllPages': false,
                            'selectCallback': function(nodes,selected){
                                // console.log( nodes, selected  );
                                rows_selected = [];
                                table.rows().every(function(index, element ){
                                    var tnode = this.node();

                                    if ( $(tnode).find('input.dt-checkboxes').is(':checked') ){
                                        rows_selected.push( $(tnode).find('input.dt-checkboxes').val() );
                                    }

                                });

                                $(".num_selected").empty();

                                if (rows_selected.length){
                                    $(".num_selected").append( "Selected: " + rows_selected.length );
                                }
                                // console.log( rows_selected );

                            },
                            // 'selectAllCallback': function(nodes,selected){
                            //     // console.log( nodes);
                            //
                            // },
                        }
                    }
                ],
                select: {
                    'style': 'multi',
                },
                order: [[2, 'asc']],
                columns: [
                    { data: null, render:
                            function(data, type, row, meta){
                                return '<input type="checkbox" class="dt-checkboxes" value="'+data.cart_id.store_key+':'+data.id+'" >';
                            },orderable : false,  "searchable": false,
                    },
                    { data: null, render: function ( data, type, row, meta ){
                            let imgurl = (data.images[0])? data.images[0].http_path : '{{ asset('css/img/no_image_275x275.jpg') }}';
                            return '<img src="'+imgurl+'" style="max-width: 60px; max-height: 60px;">'
                        }, orderable : false
                    },
                    { data: null, render:
                            function ( data, type, row, meta ){
                                return data.name + '<br><small class="text-muted more" data-toggle="popover" data-trigger="hover" data-content="'+data.description.escapeHTML()+'">'+data.description.trunc(80)+'</small>';
                            }
                    },
                    { data: null, render: function ( data, type, row, meta ){
                            return data.u_sku;
                        }},
                    { data: null, render: function ( data, type, row, meta ){
                            let owner = (data.cart_id.stores_info.store_owner_info.owner) ? data.cart_id.stores_info.store_owner_info.owner : '';
                            let email = (data.cart_id.stores_info.store_owner_info.email) ? data.cart_id.stores_info.store_owner_info.email : '';
                            return owner+'<br><small>'+email+'</small><br><small>Store Key: '+data.cart_id.store_key+'</small>';
                        }},
                    { data: null, render: function ( data, type, row, meta ){
                            let imgName = data.cart_id.cart_info.cart_name.toLowerCase().replace(/ /g,"_");
                            return '<img class="cartImage" src="https://api2cart.com/wp-content/themes/api2cart/images/logos/'+imgName+'.png"><br>' +
                                '<a href="'+data.cart_id.url+'">'+data.cart_id.url+'</a><br>'+
                                '<small>'+data.cart_id.cart_info.cart_name+'<small><br>'+
                                '<small>'+data.cart_id.cart_info.cart_versions+'</small>';

                        }},
                    { data: null, render: function ( data, type, row, meta ){
                            let Pprice = '';
                            if ( typeof data.children != 'undefined' && data.children.length ){

                                $.each(data.children, function(i, v) {
                                    Pprice += v.default_price + '&nbsp;' + data.currency + '&nbsp;<i class="fas fa-tags" style="font-size: 8px;" title="This is price of product vsriant '+ v.name +'"></i><br>';
                                });

                            } else {
                                Pprice = data.price + '&nbsp;' + data.currency;
                            }
                            return Pprice;
                        }
                    },

                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="popover"]').popover('hide');
                    $('[data-toggle="popover"]').popover({
                        html: true
                    });
                    // reinitActions();





                }
            } );





        } );
    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('parts.sidebar')

            <div class="col-lg-10">

                <div class="card">
                    <div class="card-header">Automatic emails sending <span class="ajax_status"></span></div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h1>Automatic emails sending</h1>
                                <p>Example: Send emails to e-stores’ customers and subscribers featuring new products</p>
                                <p>Providing e-store owners with the possibility to send emails automatically to their customers and subscribers is one of the crucial functions of marketing automation software providers. With API2Cart product.list, customer.list and subscriber.list methods you can do it easily!</p>
                                <p class="text-center"><img class="img-fluid" src="{{ asset('images/automatuc-emails-sending.jpg') }}" style="max-height: 300px;"></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <button class="btn btn-outline-secondary" id="">Get new products</button>
                                <button class="btn btn-outline-secondary" id="">Generate Email</button>
                                <button class="btn btn-outline-secondary" id="">Send Test Email</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-right api_log">
                                <a href="#" id="showApiLog" >Performed <span>0</span> requests with API2Cart. Click to see details...</a><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab" aria-controls="home" aria-selected="true">Products</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="subscribers-tab" data-toggle="tab" href="#subscribers" role="tab" aria-controls="profile" aria-selected="false">Subscribers</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="customers-tab" data-toggle="tab" href="#customers" role="tab" aria-controls="contact" aria-selected="false">Customers</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="template-tab" data-toggle="tab" href="#template" role="tab" aria-controls="contact" aria-selected="false">Mail Template</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <br>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table id="pdtable" class="table table-bordered" style="width: 100%; font-size: 12px;">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Image</th>
                                                    <th>Name/Description</th>
                                                    <th>SKU</th>
                                                    <th>Store Owner</th>
                                                    <th>Store</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="subscribers" role="tabpanel" aria-labelledby="subscribers-tab">...</div>
                            <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">...</div>
                            <div class="tab-pane fade" id="template" role="tabpanel" aria-labelledby="template-tab">...</div>
                        </div>





                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection