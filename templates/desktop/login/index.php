<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="description" content="" />
        <!--[if lt IE 7]><script>window.top.location = 'ie.html';</script><![endif]-->
        <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap-modal-bs3patch.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap-modal.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/datatables.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/jquery.gridster.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/jquery-ui.min.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap-select.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/datetimepicker.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/component.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/reset.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/desktop.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/windows.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/login.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/jquery.contextmenu.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/toastr.min.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/sidebar.css" />
        <link rel="stylesheet" href="/templates/desktop/assets/css/styles.css" />
        <!--[if lt IE 9]><link rel="stylesheet" href="/templates/default/assets/css/ie.css" /><![endif]-->
        <link rel="icon" type="image/x-icon" href="/templates/default/assets/images/favicon.ico" />
    </head>
    <style>
        .inner-bg {
            padding: 120px 0 170px;
        }
        .top-content .text {
            color: #fff;
            padding-top: 120px;
        }
        h1, h2 {
            color: #ccc;
            font-size: 38px;
            font-weight: 100;
            line-height: 50px;
            margin-top: 10px;
        }
        strong {
            font-weight: 500;
        }
        .top-content .top-big-link {
            margin-top: 35px;
        }
        .top-content .description {
            margin: 20px 0 10px;
        }
        .top-content .description p {
            opacity: 0.8;
        }
        .form-box {
            padding-top: 120px;
        }

        .medium-paragraph {
            font-size: 18px;
            line-height: 32px;
        }

        .colored-text { color: #42bfc2; }

        ::-moz-selection { background: #42bfc2; color: #fff; text-shadow: none; }
        ::selection { background: #42bfc2; color: #fff; text-shadow: none; }
        .form-top {
            overflow: hidden;
            padding: 0 25px 15px 25px;
            background: #444;
            background: rgba(0, 0, 0, 0.35);
            text-align: left;
        }

        .form-top-left {
            float: left;
            width: 75%;
            padding-top: 25px;
        }

        .form-top-left h3 { margin-top: 0; color: #fff; }
        .form-top-left p { opacity: 0.8; color: #fff; }

        .form-top-right {
            float: left;
            width: 25%;
            padding-top: 5px;
            font-size: 70px;
            color: #fff;
            line-height: 100px;
            text-align: right;
            opacity: 0.3;
        }

        .form-bottom {
            padding: 25px 25px 30px 25px;
            background: #444;
            background: rgba(0, 0, 0, 0.3);
            text-align: left;
        }

        .form-bottom form textarea {
            height: 100px;
        }

        .form-bottom form .form-links {
            color: #fff;
            display: inline-block;
            float: left;
            opacity: 0.8;
            padding-top: 21px;
            text-align: left;
            vertical-align: top;
            width: 275px;
        }

        #verification {
            text-transform: uppercase;
        }
        .form-bottom form .form-links a { color: #fff; }
        .form-bottom form .form-links a:hover,
        .form-bottom form .form-links a:focus { color: #fff; border-bottom: 1px dotted #fff; }

    </style>
    <body>
    <div class="top-content">
        <div class="inner-bg">
            <div class="container">
                <div class="row">
                    <div class="col-sm-7 text">
                        <h1 style="visibility: visible;"><? echo __('Welcome to');?> <strong><? echo __('Fpanel');?></strong></h1>
                        <div class="description" style="visibility: visible; animation-name: fadeInLeftBig;">
                            <h3>
                                <? echo __('Automated server control panel');?>
                            </h3>
                        </div>
                        <div class="top-big-link" style="visibility: visible; ">
                            <a href="https://kovengo.com" class="btn btn-link-1 scroll-link">Konvengo</a>
                            <a href="https://fpanel.net" class="btn btn-link-2 scroll-link">Learn more</a>
                        </div>
                    </div>
                    <div class="col-sm-5 form-box" style="visibility: visible; ">
                        <? if($this->flashSession->has("error")) { ?>
                            <div class="alert alert-danger"><button class="close" data-close="alert"></button><span><? echo $this->flashSession->output(); ?></span></div>
                        <? } ?>

                        <? if(isset($captcha_error)) { ?>
                            <div class="alert alert-danger"><button class="close" data-close="alert"></button><span><? echo $captcha_error; ?></span></div>
                        <? } ?>
                        <div class="form-top">
                            <div class="form-top-left">
                                <h3><? echo __('Log In');?></h3>
                                <p>Fill in the form below to get instant access:</p>
                            </div>
                            <div class="form-top-right">
                                <span class="typcn typcn-pencil" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="form-bottom">

                            <form class="m-t" method="POST" action="/" role="form" autocomplete="on" id="login-form">
                                <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                <div class="form-group">
                                    <label for="username" class="sr-only"><? echo __('Username');?></label>
                                    <? echo $form->render("username",array('class' => 'form-control','type' => 'text', 'placeholder' => __('Username'))) ?>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="sr-only"><? echo __('Password');?></label>
                                    <? echo $form->render("password",array('class' => 'form-control','type' => 'password', 'placeholder' => __('Password'))) ?>
                                </div>
                                <div class="form-group">
                                    <label for="lang_select" class="sr-only"><? echo __('Language');?></label>
                                    <?= $form->render("lang_select",array('class' => 'form-control form-control-sm')) ?>
                                </div>
                                <div class="form-group">
                                    <label for="templates" class="sr-only"><? echo __('Template');?></label>
                                    <? echo $form->render("templates",array('class' => 'form-control')) ?>
                                </div>
                                <div class="form-links">
                                    <a data-modal-id="modal-privacy" class="launch-modal" href="#">Privacy Policy</a> -
                                    <a data-modal-id="modal-faq" class="launch-modal" href="#">FAQ</a>
                                </div>
                                <button type="submit" class="btn btn-primary block full-width m-b"><? echo __('Login');?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var socket;
        var lvar = {
            submit : '<? echo __('Submit');/*Tamam*/?>',
            remove : '<? echo __('Delete');/*Sil*/?>',
            edit : '<? echo __('Edit');/*Düzenle*/?>',
            cancel : '<? echo __('Cancel');/*İptal*/?>',
            close : '<? echo __('Close');/*Kapat*/?>',
            help : '<? echo __('Help');/*Yardım*/?>',
            refresh : '<? echo __('Refresh');/*Yenile*/?>',
            search : '<? echo __('Search');/*Arama*/?>',
            confirm : '<? echo __('Confirm');/*Onayla*/?>',
            searching : '<? echo __('Searching');/*Aranıyor*/?>',
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
            visible_columns : '<? echo __('Columns');/*Kolonlar*/?>',
            export_excel : '<? echo __('Export Excel');/*Excele Aktar*/?>',
            export_pdf : '<? echo __('Export PDF');/*PDFe Aktar*/?>',
            print : '<? echo __('Print');/*Yazdır*/?>',
            dt_showing_info : '<? echo __('Showing _START_ to _END_ of _TOTAL_ entries');/*Toplam _TOTAL_ kayıttan _START_ ile _END_ arası kayıtlar gösteriliyor*/?>',
            filtered : '<? echo __('Filtered from _MAX_ records');/*_MAX_ Kayıttan filtrelendi*/?>',
            loading : '<? echo __('Loading');/*Yükleniyor*/?>',
            offline : '<? echo __('Offline');/*Çevrim Dışı*/?>',
            online : '<? echo __('Online');/*Çevrimiçi*/?>',
        };
    </script>
    <script src="/templates/desktop/assets/js/jquery-1.12.4.min.js"></script>
    <? if(isset($captcha) && $captcha == true)  { ?>
        <script src="https://www.google.com/recaptcha/api.js?trustedtypes=true&render=<?= $this->config->recaptcha->site_key;?>"></script>
        <script>
            $('#login-form').submit(function(event) {
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?= $this->config->recaptcha->site_key;?>', {action: '<?= md5($this->crypto->bencode($this->session->getId()));?>'}).then(function(token) {
                        $('#login-form').prepend('<input type="hidden" name="token" value="' + token + '">');
                        $('#login-form').unbind('submit').submit();
                    });
                });
                event.preventDefault();
            });
        </script>
    <? } ?>

    <script src="/templates/desktop/assets/js/jquery-ui.min.js"></script>
    <script src="/templates/desktop/assets/js/bootstrap.min.js"></script>
    <!--<script src="/templates/desktop/assets/js/modernizr-custom.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/bootbox.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/datatables.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/buttons.colVis.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/joinable.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/dataTables.colReorder.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/formvalidator.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/ajax.reload.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/jquery.inputmask.bundle.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/bootstrap-modalmanager.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/bootstrap-modal.js"></script>-->
    <!---->
    <!--<script src="/templates/desktop/assets/js/moment.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/datetimepicker.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/jquery.cokie.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/jquery.contextMenu.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/toastr.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/icheck.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/clock.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/jquery.gridster.min.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/sidebar.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/sticky.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/jquery.desktop.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/core.js"></script>-->
    <!--<script src="/templates/desktop/assets/js/debug.js"></script>-->
    </body>
</html>
<?php  exit(0); ?>
