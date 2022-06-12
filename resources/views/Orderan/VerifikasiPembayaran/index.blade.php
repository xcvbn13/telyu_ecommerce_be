@extends('layouts.app')

@section('content')
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    {{ __('You are Admin') }}
                </div>
            </div>
        </div>
    </div>
</div> --}}

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verifikasi Pembayaran</h1>
    </div>

    <!-- Content Row -->

    <!-- Modal Edit Kategori -->
    <div class="modal fade text-left" id="detailVerifikasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Detail Verifikasi</h4>
                    <button type="button" class="close" onclick="modalhide()"  data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <input type="hidden" id="id_pembayaran" name="id_pembayaran">
                        <label>Harga</label>
                        <div class="form-group">
                            <input type="text" id="harga" class="form-control" readonly/>
                        </div>
                        <label>Metode Pembayaran</label>
                        <div class="form-group">
                            <input type="text" id="metode" class="form-control" readonly/>
                        </div>
                        <label>Bukti Pembayaran</label>
                        <div class="form-group">
                            <img class="img-thumbnail" id="imgPembayaran" alt="bukti_pembayaran">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="button_gagal_verifikasi" class="btn btn-danger" data-dismiss="modal">Gagal</button>
                        <button type="button" id="button_berhasil_verifikasi" class="btn btn-primary" data-dismiss="modal">Berhasil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Verifikasi Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th style="padding-left: 1.7rem">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order as $item)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-uppercase">
                                    {{ $item->cart->user->name }}    
                                </div>
                                <div class="font-italic" style="font-size: 11pt">
                                    {{ $item->no_resi }}    
                                </div>
                            </td>
                            <td class="align-middle">{{ $item->jumlah_harga }}</td>
                            <td class="align-middle"><span class="badge badge-pill badge-warning p-2">{{ $item->status_order->status }}</span></td>
                            <td class="align-middle">
                                <a href="#" class="btn btn-success btn-circle btn-sm" 
                                data-id="{{ $item->id }}" onclick="show($(this))">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="{{ url('admin/dashboard/detail',$item->id) }}" class="btn btn-warning btn-circle btn-sm">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
@endsection

@section('cssstyle')
    <!-- Custom styles for this page -->
    <link href="{{ asset('/assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection


@section('script')
    {{-- sweetalert  --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Page level plugins -->
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('assets/js/demo/datatables-demo.js') }}"></script>

    <script>
        // edit kategori 
        function show(e) {
            let id = e.attr('data-id')
            var url = `{{ url('admin/order/verfikasi_pembayaran/detail','id') }}`;
            url = url.replace('id', id);

            $.ajax({
                type: "GET",
                url: url,
                success: function(results) {
                    console.log(results)
                    $('#detailVerifikasi').modal('show')
                    $('#id_pembayaran').val(results.id)
                    $('#harga').val(results.jumlah_harga)
                    $('#metode').val(results.metodepembayaran.metode + " - " + results.metodepembayaran.no_rek)
                    $("#imgPembayaran").attr("src", "/data_img_pembayaran/" + results.pembayaran.bukti_pembayaran);
                }
            });
        }

        function modalhide(){
            $('#detailVerifikasi').modal('hide');
        }

        // edit kategori 
        $('#button_gagal_verifikasi').click(function() {
            let id = $('#id_pembayaran').val()
            var url = `{{ url('admin/order/verifikasi_gagal','id') }}`;
            url = url.replace('id', id);
            var token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Apa Kamu Yakin?',
                text: "Kamu tidak bisa mengubah data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oke'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            "_token": token
                        },
                        success: function(results) {
                            console.log(results);
                            if (results === 'success') {
                                Swal.fire(
                                        'Berhasil!',
                                        'Pembayaran Tidak Sah',
                                        'success'
                                    ),
                                    setTimeout(function() { // wait for 5 secs(2)
                                        location.reload(); // then reload the page.(3)
                                    }, 1000);
                            }
                        }
                    });

                }
            })
        })

        $('#button_berhasil_verifikasi').click(function() {
            let id = $('#id_pembayaran').val()
            var url = `{{ url('admin/order/pembayaran_terverifikasi','id') }}`;
            url = url.replace('id', id);
            var token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Apa Kamu Yakin?',
                text: "Kamu tidak bisa mengubah data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oke'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            "_token": token
                        },
                        success: function(results) {
                            console.log(results);
                            if (results === 'success') {
                                Swal.fire(
                                        'Berhasil!',
                                        'Pembayaran Sah',
                                        'success'
                                    ),
                                    setTimeout(function() { // wait for 5 secs(2)
                                        location.reload(); // then reload the page.(3)
                                    }, 1000);
                            }
                        }
                    });

                }
            })
        })
    </script>
@endsection
