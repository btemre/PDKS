<?php
include_once '../../init.php';
$izin_id  = $adminclass->g("izin_id");
$id        =$adminclass->g("izin_id");
$variable = $adminclass->getAyar();

$sql = "SELECT personel_durumid, izin_turid, personel_sozlesmelimi FROM izin AS i
LEFT JOIN personel AS p On p.personel_id = i.izin_personel
WHERE izin_id = $izin_id AND izin_kurumid = $_SESSION[kullanici_kurumid]";

$query = $adminclass->pdoQuery($sql);

$personel_durumid       = $query[0]["personel_durumid"];
$izin_turid             = $query[0]["izin_turid"];
$personel_sozlesmelimi  = $query[0]["personel_sozlesmelimi"];

if ($personel_durumid == 1 && $izin_turid == 1 && $personel_sozlesmelimi == 0) {
include_once "yazdir_memur.php";

} else if ($personel_durumid == 2 && $izin_turid == 1 && $personel_sozlesmelimi == 0) {
include_once "yazdir_isci.php";

} else if ($personel_durumid == 5 && $izin_turid == 1) {
include_once "yazdir_firma.php";

} else if ($personel_durumid == 1 && $izin_turid == 1 && $personel_sozlesmelimi == 1) {
include_once "yazdir_sozlesmeli.php";

} else if ($personel_durumid == 3 && $izin_turid == 1 && $personel_sozlesmelimi == 0) {
include_once "yazdir_surekliisci.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi == 0 && $izin_turid == 6) {
include_once "yazdir_memurvefat.php";

} else if ($personel_durumid == 2 /*&& $personel_sozlesmelimi==0*/ && $izin_turid == 6) {
include_once "yazdir_iscivefat.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi==0 && $izin_turid == 3) {
include_once "yazdir_memurrapor.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi==1 && $izin_turid == 3) {
    include_once "yazdir_sozlesmelirapor.php";

} else if ($personel_durumid == 2 /*&& $personel_sozlesmelimi==0*/ && $izin_turid == 3) {
    include_once "yazdir_iscirapor.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi == 0 && $izin_turid == 9) {
include_once "yazdir_memurevlilik.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi == 1 && $izin_turid == 9) {
include_once "yazdir_sozlesmelievlilik.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi == 1 && $izin_turid == 8) {
include_once "yazdir_sozlesmelibabalik.php";

} else if ($personel_durumid == 1 && $personel_sozlesmelimi == 0 && $izin_turid == 8) {
include_once "yazdir_memurbabalik.php";

} else if ($personel_durumid == 2 && $izin_turid == 8) {
include_once "yazdir_iscibabalik.php";

} else if ($personel_durumid == 1 && $izin_turid == 7) {
include_once "yazdir_memurrefakat.php";
}
?>

<script>

    window.print();
</script>
