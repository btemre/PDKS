<?php
$sql = "SELECT i.izin_saglikkurumu,d.durum_ad,u.unvan_ad,p.personel_adsoyad,p.personel_tc,p.personel_sicilno,it.izin_ad,i.izin_tarih,
i.izin_adresi,i.izin_yil,i.izin_baslayis,i.izin_suresi,p.personel_telefon from izin_calisan_haklari ich
inner JOIN durum d on d.durum_id=ich.calisan_statu_id
inner JOIN personel p on p.personel_durumid=d.durum_id
inner JOIN izin i on i.izin_personel=p.personel_id and ich.izin_tur_id=i.izin_turid
inner JOIN unvan u on u.unvan_id=p.personel_unvan 
inner JOIN izin_turleri it on it.izin_turid=ich.izin_tur_id
WHERE p.personel_durum='1'
and i.izin_durum='1'
and p.personel_durumid='1' 
and i.izin_id=$id";
$query = $adminclass->pdoQuery($sql);
?>
<head>
    <title>Sözleşmeli Personel Hastalık İzin Formu </title>
</head>
<table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
    <tr>
        <td width="1060" height="100">
            <p align="left"><b><font face="Times New Roman"><span style="font-size:16pt;">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;T.C.<br>
                KARAYOLLARI GENEL MÜDÜRLÜĞÜ<br>
                &emsp;&emsp;&emsp;&emsp;5. BÖLGE MÜDÜRLÜĞÜ<br><br>
            </span></font></b></p>
        </td>
    </tr>
</table>
<table border="2" width="904" height="1310" bordercolor="black" cellspacing="0" cellpadding="0">
    <td width="895" height="51" colspan="3">
        <table border="0" cellpadding="0" cellspacing="0">
            <p align="center"><b><span style="font-size:16pt;">SÖZLEŞMELİ PERSONEL HASTALIK İZİN FORMU</span></b></p>
        </table>
    </td>

    <tr>
        <td width="93" height="90" rowspan="3">
            <p align="center"><font style="writing-mode:rl-tb"><b>HASTALIK<br>
            </b></font><b>İZNİ<br>
            KULLANANIN</b></p>
        </td>
        <td width="369" height="29">
                <b><p>&nbsp;ADI SOYADI:</p></b>
        </td>
        <td width="422" height="29"><span style="font-size:14pt;">&nbsp; <?= $query[0]['personel_adsoyad'] ?></span></td>
    </tr>
    <tr>
        <td width="369" height="30">
            <b><p>&nbsp;SİCİL NO:</p></b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; <?= $query[0]['personel_sicilno']; ?></span></td>
    </tr>
    <tr>
        <td width="369" height="30">
            <b><p>&nbsp;ÇALIŞTIĞI BİRİM:</p></b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; <?= $variable[0]['ayar_kurum']; ?></span></td>
    </tr>
    <tr>
        <td width="468" height="30" colspan="2">
            <b><p>&nbsp;&nbsp;SAĞLIK KURUMUNUN ADI:</p></b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; <?= $query[0]['izin_saglikkurumu']; ?></span></td>
    </tr>
    <tr>
        <td width="468" height="30" colspan="2">
            <b><p>&nbsp;&nbsp;HASTALIK İZİN SÜRESİ:</p></b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; <?= $query[0]['izin_suresi']; ?> (<?= yaziylasayi($query[0]['izin_suresi']) ?>) Gün</span></td>
    </tr>
    <tr>
        <td width="895" height="528" colspan="3">
            <table border="0" cellpadding="0" cellspacing="0" width="1061" height="423">


                <tr>
                    <td width="1061" height="51" colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td width="1062" height="67" colspan="5">
                        <p align="left"><font face="Times New Roman" style="font-size:14pt;"><span> 

                            &emsp;&emsp;&emsp;&emsp;&emsp;Sözleşmeli Personel Çalıştırılmasına İlişkin Esasların 9. maddesi  uyarınca <?= date("d/m/Y",strtotime($query[0]['izin_baslayis'])) ?> tarihinden <br><br>&emsp; &emsp;
                                    itibaren <?= $query[0]['izin_suresi'] ?> (<?= yaziylasayi($query[0]['izin_suresi']) ?>) gün süre ile izinli sayılmasını arz ederim. ...../...../<?php echo date("Y"); ?>
                            <br> <br>
                        &nbsp;</span></font></p>
                    </td>
                </tr>

                <tr>
                    <td width="1061" height="29" colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td width="779" height="6" colspan="3">&nbsp;</td>
                    <td width="282" height="6" colspan="2">
                        <p align="center"><b><font face="Times New Roman"><span style="font-size:14pt;"><?= $variable[0]['ayar_yonetici'] ?></span></font></b></p>
                    </td>
                </tr>
                <tr>
                    <td width="779" height="11" colspan="3">&nbsp;</td>
                    <td width="282" height="11" colspan="2">
                        <p align="center"><b><font face="Times New Roman"><span style="font-size:14pt;"><?= $variable[0]['ayar_yoneticiunvan'] ?></span></font></b></p>
                    </td>
                </tr>
                <tr>
                </tr>
            </table>
            <p align="center"><font style="font-size:14pt;"><b>UYGUNDUR</b></font></p><br>
            <p align="center"><font style="font-size:14pt;"><b>...../...../<?php echo date("Y"); ?><br><br>
        </td>
    </tr>
    <tr>
        <td width="895" height="265" colspan="3">
            <table border="0" cellpadding="0" cellspacing="0" width="1061" height="423">
                <tr>
                    <td width="1061" height="10" colspan="5">

                    </td>
                </tr>



                <tr>
                    <td width="109" height="19">
                        <p align="right"><b>Sayı</b></p>
                    </td>
                    <td width="816" height="19" colspan="3">: 35557330/</td>
                    <td width="136" height="19">......./......../<font face="Times New Roman"><span style="font-size:14pt;"><?= date("Y"); ?></span></font></td>
                </tr>
                <tr>
                    <td width="1061" height="74" colspan="5">
                        <!-- <p>&nbsp;</p> -->
                        <p align="center"><b>
                                <font face="Times New Roman"><span style="font-size:14pt;">PERSONEL ŞUBESİ MÜDÜRLÜĞÜNE</span></font>
                            </b></p>
                    </td>
                </tr>
                     </td>
                 </tr>

                 <tr>
                    <td width="109" height="16"><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;</span></font></td>
                    <td width="603" height="16"><font face="Times New Roman"><span style="font-size:14pt;"></span></font></td>
                    <td width="349" height="16" colspan="3"><font face="Times New Roman"><span style="font-size:14pt;"></span></font></td>
                </tr>
                <tr>
                    <td width="1061" height="51" colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td width="1061" height="29" colspan="5">
                        <p align="left"><font style="font-size:14pt;" face="Times New Roman">&emsp;&emsp;&emsp;Adı geçen personel  ...../...../<?= date("Y"); ?>  ile  ....../....../<?= date("Y"); ?> tarihleri arasında (......) gün Hastalık iznini kullanarak görevine başlamıştır.</font></p> <br>
                    </td>
                </tr>
                <tr>
                    <td width="1061" height="10" colspan="5"><font style="font-size:14pt;" face="Times New Roman">&emsp;&emsp;&emsp;Bilgilerine arz ederim.</font></td>
                </tr>
                <tr>
                    <td width="1061" height="29" colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td width="779" height="6" colspan="3">&nbsp;</td>
                    <td width="282" height="6" colspan="2">
                        <p align="center"><b><font face="Times New Roman"><span style="font-size:14pt;"><?= $variable[0]['ayar_mudur'] ?></span></font></b></p>
                    </td>
                </tr>
                <tr>
                    <td width="779" height="11" colspan="3">&nbsp;</td>
                    <td width="282" height="11" colspan="2">
                        <p align="center"><b><font face="Times New Roman"><span style="font-size:14pt;"><?= $variable[0]['ayar_mudurunvan'] ?></span></font></b></p> <br><br><br><br>
                    </td>
                </tr>
                <tr>
                    <td width="779" height="3" colspan="3">&nbsp;</td>
                    <td width="282" height="3" colspan="2">&nbsp;</td>

                </tr>
            </table>
        </td>
    </tr>
    <table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
        <tr>
            <td width="1060" height="10">
                <font style="font-size:10pt;" face="Times New Roman"><b>Form Stok No:</b>7582002
                </font>
            </td>
        </tr>

    </table>