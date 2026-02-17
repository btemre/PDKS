<style>
    td.dtr-control::before {
    content: "+";
    color: #007bff;
    display: inline-block;
    margin-right: 5px;
    cursor: pointer;
}
tr.parent td.dtr-control::before {
    content: "-"; /* açılmış durumda simge değişir */
}

</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <h5>İzinli / Raporlu Personeller</h5>
                </div>
            </div>
        
            <div class="card-body">
                <div class="row g-4 mb-3">
                    <div class="col-sm-auto">
                    </div>
                </div>
                <table id="table-default"
                    class="table table-bordered dt-responsive nowrap table-striped align-middle"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:5px;">Sıra</th>
                            <th>Ad Soyad</th>
                            <th>Statü</th>
                            <th>Ünvan</th>
                            <th>Dönem</th>
                            <th>İzin Tür</th>
                            <th>İzin Başlama</th>
                            <th>İzin Bitiş</th>
                            <th>İşe Başlama</th>
                            <th>Süresi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach($izinli as $izin)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $izin->personel_adsoyad }}</td>
                            <td>{{ $izin->durum_ad }}</td>
                            <td>{{ $izin->unvan_ad }}</td>
                            <td>{{ $izin->izin_yil }}</td>
                            <td>{{ $izin->izin_ad }}</td>
                            <td>{{ \Carbon\Carbon::parse($izin->izin_baslayis)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($izin->izin_bitis)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($izin->izin_isebaslayis)->format('d-m-Y') }}</td>
                            <td>{{ $izin->izin_suresi }} gün</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#table-default').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
        });
    });
    </script>
    