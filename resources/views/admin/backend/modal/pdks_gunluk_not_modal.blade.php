<div class="modal fade" id="pdksGunlukNotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title"><i class="mdi mdi-note-text-outline me-1"></i> Günlük Not</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pdks_not_personel_id">
                <input type="hidden" id="pdks_not_tarih">
                <input type="hidden" id="pdks_not_tip">
                <div class="mb-3">
                    <label class="form-label">Açıklama / Not</label>
                    <textarea id="pdks_not_aciklama" class="form-control" rows="3" maxlength="500" placeholder="Geç gelme / erken çıkış için kısa not..."></textarea>
                    <small class="text-muted">Maksimum 500 karakter.</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="pdks_not_kaydet"><i class="mdi mdi-content-save me-1"></i> Kaydet</button>
            </div>
        </div>
    </div>
</div>
