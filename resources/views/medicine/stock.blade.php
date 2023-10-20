@extends('layouts.template')

@section('content')
<div id="msg-success"></div>

<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Stok</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach ($medicines as $item)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $item['name'] }}</td>
            <td style="{{ $item['stock'] <=3 ? 'background: red; color:white' : 'background: none; color: black'}}">{{ $item ['stock'] }}</td>
            <td class="d-flex justify-content-center">
                <div onclick="edit('{{ $item['id'] }}')" class="btn btn-primary me-3" style="cursor: pointer">Tambah Stock</div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="modal" tabindex="-1" id="edit-stock">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Data Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="form-stock">
                <div class="modal-body">
                    <div id="msg"></div>

                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Obat :</label>
                        <input type="text" class="form-control" id="name" name="name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock Obat :</label>
                        <input type="number" class="form-control" id="stock" name="stock">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script') 
    <script type="text/javascript"> 

        $.ajaxSetup({ //direload hanya halaman itu aja
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        function edit(id) { //function yg memunculkan modal
            var url = "{{ route('medicine.stock.edit', ":id") }}"; //root yg mana yg akan dituju (yg mau di get)
            url = url.replace(':id', id) 
            $.ajax({ //meminta permintaan ajax 
                type:"GET",
                url: url,
                dataType: 'json', //bentuknya javascript jadi json
                success: function (res) { // untuk ngeget
                    $('#edit-stock').modal('show') // modal, mau di show
                    $('#id').val(res.id) 
                    $('#name').val(res.name) //memanggil value (#) dan id(name)
                    $('#stock').val(res.stock) 
                }
            });
        }

        $('#form-stock').submit(function(e) {
            
            e.preventDefault(); 

            var id = $('#id').val(); // ngeget id 
            var urlform = "{{ route('medicine.stock.update', ":id") }}"; // karna mau ngeupdate jd manggil update
            urlform = urlform.replace(':id', id); // fungsinya sm kaya by 

            var data = { //menyimpan stock yg dari id methodnya fetch 
                stock:$('#stock').val(), //karna yg mau di update yg stocknya aja jd manggil stock doang
            }


            $.ajax({
                type:'PATCH',
                url: urlform,
                data: data,
                cache: false,
                success: (data) => {
                    $("#edit-stock").modal('hide'); //kalo berhasil nanti hide nya ada
                    sessionStorage.reloadAfterPageLoad = true; //akan menjadi penyimpanan setiap session secara sementara dan akan kedaluarsa kalo webnya ditutup, kalo selesai jadi akan ngereload lagi
                    window.location.reload(); 
                },
                error: function(data){
                    $('#msg').attr("class", "alert alert-danger"); //kalo error akan menampilkan alert
                    $('#msg').text(data.responseJSON.message);
                }
            });
        });

        $(function () {
            if (sessionStorage.reloadAfterPageLoad) { //kalo kondisi atas terpenuhi akan memunculkan alert, dan di clear
                $('#msg-success')   .attr("class", "alert alert-succes");
                $('#msg-success').text("Berhasil menambahkan data stock!");
                sessionStroage.clear();
            }
        })

</script>
@endpush