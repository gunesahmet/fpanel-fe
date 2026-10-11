<script>
    var socket;
    var lvar = {
        success : '<? echo __('Success');/*Başarılı*/?>',
        copied : '<? echo __('Copied');/*Kopyalandı*/?>',
        submit : '<? echo __('Submit');/*Tamam*/?>',
        remove : '<? echo __('Delete');/*Sil*/?>',
        edit : '<? echo __('Edit');/*Düzenle*/?>',
        cancel : '<? echo __('Cancel');/*İptal*/?>',
        error : '<? echo __('Error');/*Hata*/?>',
        close : '<? echo __('Close');/*Kapat*/?>',
        help : '<? echo __('Help');/*Yardım*/?>',
        refresh : '<? echo __('Refresh');/*Yenile*/?>',
        search : '<? echo __('Search');/*Arama*/?>',
        confirm : '<? echo __('Confirm');/*Onayla*/?>',
        searching : '<? echo __('Searching');/*Aranıyor*/?>',
        dashboard : '<? echo __('Dashboard');/*Ana Sayfa*/?>',
        all : '<? echo __('All');/*Tümü*/?>',
        open : '<? echo __('Open');/*Açık*/?>',
        are_you_sure : '<? echo __('Are you sure?');/*Emin misiniz?*/?>',
        add_shortcut : '<? echo __('Add to Shortcut');/*Kısayol Ekle*/?>',
        create_folder : '<? echo __('Create Folder');/*Klasör Oluştur*/?>',
        change_interface : '<? echo __('Change Interface');/*Arayüzü Değiştir*/?>',
        account_settings : '<? echo __('Account Settings');/*Hesap Ayarları*/?>',
        enter_3_character : '<? echo __('You must enter at least 3 characters');/*En az 3 karakter girmelisiniz*/?>',
        remove_from_shortcut : '<? echo __('Remove from shortcut');/*Kısayolu kaldır*/?>',
        forgot_password : '<? echo __('Forgot your password');/*Parolanızı mı unuttunuz*/?>',
        login : '<? echo __('Log In');/*Giriş*/?>',
        password : '<? echo __('Password');/*Parola*/?>',
        username : '<? echo __('Username');/*Kullanıcı Adı*/?>',
        please_login : '<? echo __('Please Login');/*Lütfen Giriş Yapınız*/?>',
        select_unit : '<? echo __('Select an Unit');/*Birim Seçiniz*/?>',
        select_language : '<? echo __('Select a Language');/*Dil Seçiniz*/?>',
        detailed_patient_search : '<? echo __('Detailed Patient Search');/*Detaylı Hasta Arama*/?>',
        empty_table : '<? echo __('No data available');/*Veri bulunamadı*/?>',
        no_entries_to_show : '<? echo __('No Entries to show');/*Gösterilecek kayıt bulunamadı*/?>',
        zero_records : '<? echo __('No records');/*Kayıt bulunamadı*/?>',
        select_one : '<? echo __('Select one');/*Seçiniz*/?>',
        emergency_informations_form : '<? echo __('Emergency Informations Form');/*Acil Bilgileri Formu*/?>',
        wrong_identity_no : '<? echo __('Identity number is wrong');/*Hatalı TC Kimlik Numarası*/?>',
        copy : '<? echo __('Copy');/*Kopyala*/?>',
        firstboot : '<? echo $firstboot;?>',
        visible_columns : '<? echo __('Columns');/*Kolonlar*/?>',
        export_excel : '<? echo __('Export Excel');/*Excele Aktar*/?>',
        export_pdf : '<? echo __('Export PDF');/*PDFe Aktar*/?>',
        print : '<? echo __('Print');/*Yazdır*/?>',
        dt_showing_info : '<? echo __('Showing _START_ to _END_ of _TOTAL_ entries');/*Toplam _TOTAL_ kayıttan _START_ ile _END_ arası kayıtlar gösteriliyor*/?>',
        filtered : '<? echo __('Filtered from _MAX_ records');/*_MAX_ Kayıttan filtrelendi*/?>',
        loading : '<? echo __('Loading');/*Yükleniyor*/?>',
        offline : '<? echo __('Offline');/*Çevrim Dışı*/?>',
        online : '<? echo __('Online');/*Çevrimiçi*/?>',
        create_file : '<? echo __('Create File');/*Dosya Oluştur*/?>',
        new_name : '<? echo __('New Name');/*Yeni İsim*/?>',
        rename_file : '<? echo __('Rename Name');/*Yeniden İsimlendir*/?>',
        name : '<? echo __('Name');/*İsim*/?>',
        modified : '<? echo __('Modified');/*Düzenlenme Tarihi*/?>',
        size : '<? echo __('Size');/*Boyut*/?>',
        enter_valid_ip : '<? echo __('Enter Valid IP Address');/*Geçerli bir ip adresi giriniz*/?>',
    };
    var btn_links = '<? echo $this->uri->url_encrypt("dashboard/buttons");?>';
    var ss = '<? echo $ss;?>';
    var language_lists = '<? echo json_encode([]);?>';
    var selected_lang = '<? echo $this->fcookies->get('language');?>';
</script>
<script src="/templates/desktop/assets/js/jquery-3.1.0.min.js"></script>
<script src="/templates/desktop/assets/js/jquery-ui.min.js"></script>
<script src="/templates/desktop/assets/js/bootstrap.min.js"></script>
<script src="/templates/desktop/assets/js/bootbox.min.js"></script>
<script src="/templates/desktop/assets/js/jstree.min.js"></script>
<script src="/templates/desktop/assets/js/datatables.min.js"></script>
<script src="/templates/desktop/assets/js/buttons.colVis.min.js"></script>
<script src="/templates/desktop/assets/js/dataTables.colReorder.min.js"></script>
<script src="/templates/desktop/assets/js/ajax.reload.js"></script>
<script src="/templates/desktop/assets/js/jquery.select-multiple.js"></script>
<script src="/templates/desktop/assets/js/bootstrap-modalmanager.js"></script>
<script src="/templates/desktop/assets/js/bootstrap-modal.js"></script>
<script src="/templates/desktop/assets/js/peity.min.js"></script>
<script src="/templates/desktop/assets/js/jquery.multi-select.js"></script>
<script src="/templates/desktop/assets/js/codemirror.js"></script>
<script src="/templates/desktop/assets/js/htmlmixed.js"></script>
<script src="/templates/desktop/assets/js/xml.js"></script>
<script src="/templates/desktop/assets/js/javascript.js"></script>
<script src="/templates/desktop/assets/js/php.js"></script>
<script src="/templates/desktop/assets/js/css.js"></script>


<script src="/templates/desktop/assets/js/jquery.ui.widget.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/tmpl.min.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/jquery.iframe-transport.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/jquery.fileupload.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/jquery.fileupload-process.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/jquery.fileupload-validate.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/jquery.fileupload-ui.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/file-upload.js" type="text/javascript"></script>



<script src="/templates/desktop/assets/js/filemanager.upload.js" type="text/javascript"></script>
<script src="/templates/desktop/assets/js/filemanager.js" type="text/javascript"></script>

<script src="/templates/desktop/assets/js/bootstrap-switch.min.js"></script>
<script src="/templates/desktop/assets/js/moment.min.js"></script>
<script src="/templates/desktop/assets/js/jquery.cokie.min.js"></script>
<script src="/templates/desktop/assets/js/jquery.contextMenu.min.js"></script>
<script src="/templates/desktop/assets/js/toastr.min.js"></script>
<script src="/templates/desktop/assets/js/icheck.min.js"></script>
<script src="/templates/desktop/assets/js/clock.js"></script>
<script src="/templates/desktop/assets/js/jquery.gridster.min.js"></script>
<script src="/templates/desktop/assets/js/sidebar.js"></script>
<script src="/templates/desktop/assets/js/chart.js"></script>
<script src="/templates/desktop/assets/js/jquery.desktop.js"></script>
<script src="/templates/desktop/assets/js/core.js"></script>
<script src="/templates/desktop/assets/js/jquery.bootstrap.wizard.min.js"></script>
<script>
    <? if($firstboot == true) { ?>
    core.installation();
    <? } ?>
</script>