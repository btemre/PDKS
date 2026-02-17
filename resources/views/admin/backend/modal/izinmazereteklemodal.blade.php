<div id="izinmazeret-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="IzinMazeretForm" id="IzinMazeretForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="izinmazeret_id" id="izinmazeret_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">

                            <div class="col-lg-6">
                                <div>
                                    <label for="izinmazeret_personel" class="form-label">Personel</label>
                                    <select id="izinmazeret_personel" class="form-control" name="izinmazeret_personel"
                                        required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($personel as $value)
                                            <option value="{{ $value->personel_id }}"
                                                data-calisan-tipi="{{ $value->personel_durumid }}">
                                                {{ $value->personel_adsoyad }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izinmazeret_turid" class="form-label">İzin Türü</label>
                                    <select id="izinmazeret_turid" class="form-control" name="izinmazeret_turid" required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($izintur as $value)
                                            <option value="{{ $value->izin_turid }}">{{ $value->izin_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div>
                                    <label for="izinTarihi" class="form-label">İzin Tarihi</label>
                                    <input type="date" id="izinmazeret_baslayis" name="izinmazeret_baslayis"
                                        class="form-control" required="required">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="izinBaslamaSaati" class="form-label">İzin Başlama Saati</label>
                                    <input type="time" id="izinmazeret_baslayissaat" name="izinmazeret_baslayissaat"
                                        class="form-control" required="required">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="izinBitisSaati" class="form-label">İzin Bitiş Saati</label>
                                    <input type="time" id="izinmazeret_bitissaat" name="izinmazeret_bitissaat"
                                        class="form-control" required="required">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="aciklama-field" class="form-label">Açıklama</label>
                                <input type="text" id="izinmazeret_aciklama" class="form-control"
                                    name="izinmazeret_aciklama" />
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