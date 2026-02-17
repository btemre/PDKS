const unvanTipleri = ["1", "2", "3"];

const addDays = (date, days) => {
    let result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
};

const isHoliday = date => {
    const holidays = window.resmiTatiller;
    return holidays.includes(date.toISOString().split('T')[0]);
};

const isWeekend = date => {
    return date.getDay() === 0;
};

const calculateDates = () => {
    const izinTuruId = document.getElementById("izin_turid").value;
    const izinBaslamaTarihiInput = document.getElementById("izin_baslayis").value;
    const izinSuresi = parseInt(document.getElementById("izin_suresi").value);
    const personelSelect = document.getElementById("izin_personel");
    const calisanTipi = personelSelect.options[personelSelect.selectedIndex].dataset.calisanTipi;

    if (!izinBaslamaTarihiInput || isNaN(izinSuresi) || !izinTuruId || !calisanTipi) {
        document.getElementById("izin_bitis").value = '';
        document.getElementById("izin_isebaslayis").value = '';
        return;
    }

    let izinBitisTarihi = new Date(izinBaslamaTarihiInput);
    let sayilanGun = 0;

    if (izinTuruId == 1) { // Yıllık izin türü ise
        while (sayilanGun < izinSuresi) {
            izinBitisTarihi = addDays(izinBitisTarihi, 1);
            if (!isHoliday(izinBitisTarihi) && (calisanTipi == unvanTipleri[0] || !isWeekend(izinBitisTarihi))) {
                sayilanGun++;
            }
        }
    } else { // Diğer izin türleri ise
        izinBitisTarihi = addDays(izinBitisTarihi, izinSuresi);
    }

    let iseBaslamaTarihi = new Date(izinBitisTarihi);
    while (isHoliday(iseBaslamaTarihi) || (calisanTipi == unvanTipleri[0] && isWeekend(iseBaslamaTarihi))) {
        iseBaslamaTarihi = addDays(iseBaslamaTarihi, 1);
    }

    document.getElementById("izin_bitis").value = addDays(izinBitisTarihi, -1).toISOString().split('T')[0];
    document.getElementById("izin_isebaslayis").value = iseBaslamaTarihi.toISOString().split('T')[0];
};

document.getElementById("izin_baslayis").addEventListener("change", calculateDates);
document.getElementById("izin_suresi").addEventListener("input", calculateDates);
document.getElementById("izin_turid").addEventListener("change", calculateDates);
document.getElementById("izin_personel").addEventListener("change", calculateDates);
