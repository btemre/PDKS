<style>
    /* Kartların yüksekliğini eşitle */
    .row > .col-lg-8, 
    .row > .col-lg-4 {
        display: flex;
        flex-direction: column;
    }
    .row > .col-lg-8 .card,
    .row > .col-lg-4 .card {
        flex: 1 1 auto;
    }
    </style>
    
    <div class="row">
        <!-- Geç Gelen Personeller Listesi -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>Bugün Geç Gelen Personeller</h5>
                </div>
                <div class="card-body">
                    <table id="gec-gelen-table"
                        class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5px;">Sıra</th>
                                <th>Ad Soyad</th>
                                <th>Birim</th>
                                <th>Ünvan</th>
                            
                                <th>Giriş Saati</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($gecGelen as $p)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $p->personel_adsoyad }}</td>
                                <td>{{ $p->birim_ad }}</td>
                                <td>{{ $p->unvan_ad }}</td>
                                <td>{{ saat($p->ilk_gecis)  }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
        <!-- Unvan Bazlı Geç Gelen Grafik -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Unvan Bazlı Geç Gelen Dağılımı</h5>
                </div>
                <div class="card-body">
                    <canvas id="gecGelenUnvanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    $(document).ready(function() {
        // DataTable
        $('#gec-gelen-table').DataTable({
            responsive: true,
            paging: true,
            pageLength: 3,
            lengthMenu: [3,4,5],
            searching: true,
            ordering: true,
            language: { url: '{{ url('build/json/datatabletr.json') }}' },
        });
    
        // Chart.js - geç gelen unvan dağılımı
        const labels = {!! json_encode($gecGelenUnvanGrafik->keys()) !!};
        const dataValues = {!! json_encode($gecGelenUnvanGrafik->values()) !!};


    
        const ctx = document.getElementById('gecGelenUnvanChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(255, 159, 64, 0.8)');
        gradient.addColorStop(1, 'rgba(153, 102, 255, 0.8)');
    
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Geç Gelen Personel Sayısı',
                    data: dataValues,
                    backgroundColor: gradient,
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    hoverBackgroundColor: 'rgba(75, 192, 192, 0.8)',
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { display: true },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: { 
                    y: { beginAtZero: true, precision:0 },
                    x: { ticks: { autoSkip: false } }
                }
            }
        });
    });
    </script>
    