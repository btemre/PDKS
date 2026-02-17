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
    <!-- Gelmeyen Personeller Listesi -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>Bugün Gelmeyen Personeller</h5>
            </div>
            <div class="card-body">
                <table id="gelmeyen-table"
                    class="table table-bordered dt-responsive nowrap table-striped align-middle"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:5px;">Sıra</th>
                            <th>Ad Soyad</th>
                            <th>Birim</th>
                            <th>Ünvan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach($gelmeyen as $p)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $p->personel_adsoyad }}</td>
                            <td>{{ $p->birim_ad }}</td>
                            <td>{{ $p->unvan_ad }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Unvan Bazlı Gelmeyen Grafik -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Unvan Bazlı Gelmeyen Dağılımı</h5>
            </div>
            <div class="card-body">
                <canvas id="gelmeyenUnvanChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // DataTable
    $('#gelmeyen-table').DataTable({
        responsive: true,
        paging: true,
        pageLength: 3, // default 5 kayıt
        lengthMenu: [3,4,5],
        searching: true,
        ordering: true,
        language: { url: '{{ url('build/json/datatabletr.json') }}' },
    });

    // Chart.js - tek grafikte unvan bazlı, gradient ve hover efektli
    const labels = {!! json_encode($gelmeyenUnvanGrafik->keys()) !!};
const dataValues = {!! json_encode($gelmeyenUnvanGrafik->values()) !!};


    const ctx = document.getElementById('gelmeyenUnvanChart').getContext('2d');

    // Gradient oluştur
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(255, 99, 132, 0.8)');
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0.8)');

    const gelmeyenUnvanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Gelmeyen Personel Sayısı',
                data: dataValues,
                backgroundColor: gradient,
                borderColor: 'rgba(0,0,0,0.1)',
                borderWidth: 1,
                hoverBackgroundColor: 'rgba(255, 206, 86, 0.8)',
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