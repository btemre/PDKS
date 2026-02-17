<div id="pdksgecis-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="PdksGecisForm" id="PdksGecisForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="pdksgecis_id" id="pdksgecis_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">

                            <div class="col-lg-8">
                                <div>
                                    <label for="kart_id" class="form-label">Personel</label>
                                    <select id="kart_id" class="form-control" name="kart_id" required
                                        data-ajax-url="{{ route('pdks.personel-kart-ara') }}">
                                        <option value="">Seçiniz</option>
                                    </select>
                                    <small class="text-muted">Modal açıldığında personel listesi yüklenir.</small>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="izinTarihi" class="form-label">İzin Tarihi</label>
                                    <input type="datetime-local" id="gecis_tarihi" name="gecis_tarihi"
                                        class="form-control" required="required">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-success" id="btn-save">Kaydet</button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</div>
<script>
(function() {
    var modal = document.getElementById('pdksgecis-modal');
    if (!modal) return;
    $(modal).on('hidden.bs.modal', function() {
        $('#kart_id').find('option:not(:first)').remove();
    });
    $(modal).on('shown.bs.modal', function() {
        var sel = $('#kart_id');
        var url = sel.data('ajax-url');
        if (!url) return;
        sel.find('option:not(:first)').remove();
        $.get(url, function(res) {
            if (res.results && res.results.length) {
                $.each(res.results, function(i, o) {
                    sel.append($('<option></option>').val(o.id).text(o.text));
                });
            }
        });
    });
})();
</script>