<?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */namespace FPanel\System;

/**
 * Class Templates
 * @package FPanel\System
 */
trait Templates
{
    private function requestEmailTemplate(): bool
    {
        $message_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
    <!--[if gte mso 9]><xml>
     <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
     </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <title>Fpanel</title>


</head>
<body style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #FFFFFF">
  <style id="media-query">#outlook a{padding:0}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}#backgroundTable{margin:0;padding:0;width:100%!important;line-height:100%!important}.button a{display:inline-block;text-decoration:none;-webkit-text-size-adjust:none;text-align:center}.button a div{text-align:center!important}body.outlook p{display:inline!important}@media only screen and (max-width: 500px){table[class="body"] img{height:auto!important;width:100%!important}table[class="body"] img.fullwidth{max-width:100%!important}table[class="body"] center{min-width:0!important}table[class="body"] .container{width:95%!important}table[class="body"] .row{width:100%!important;display:block!important}table[class="body"] .wrapper{display:block!important;padding-right:0!important}table[class="body"] .columns,table[class="body"] .column{table-layout:fixed!important;float:none!important;width:100%!important;padding-right:0!important;padding-left:0!important;display:block!important}table[class="body"] .wrapper.first .columns,table[class="body"] .wrapper.first .column{display:table!important}table[class="body"] table.columns td,table[class="body"] table.column td,.col{width:100%!important}table[class="body"] table.columns td.expander{width:1px!important}table[class="body"] .right-text-pad,table[class="body"] .text-pad-right{padding-left:10px!important}table[class="body"] .left-text-pad,table[class="body"] .text-pad-left{padding-right:10px!important}table[class="body"] .hide-for-small,table[class="body"] .show-for-desktop{display:none!important}table[class="body"] .show-for-small,table[class="body"] .hide-for-desktop{display:inherit!important}.mixed-two-up .col{width:100%!important}}@media screen and (max-width: 500px){div[class="col"]{width:100%!important}}@media screen and (min-width: 501px){table[class="container"]{width:500px!important}}</style>
  <table cellpadding="0" cellspacing="0" width="100%" class="body" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 100%;width: 100%;table-layout: fixed">
      <tbody><tr style="vertical-align: top">
          <td class="center" align="center" valign="top" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;text-align: center;background-color: #FFFFFF">

              <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                <tbody><tr style="vertical-align: top">
                  <td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: #D9D9D9">

                    <!--[if gte mso 9]>
                    <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td>
                    <![endif]-->
                    <!--[if (IE)]>
                    <table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                    <![endif]-->

                    <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit"><tbody><tr style="vertical-align: top"><td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0"><!--[if (gte mso 9)|(IE)]><table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0"><tr><![endif]--><!--[if (gte mso 9)|(IE)]><td valign="top" width="500" style="width:500px;"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%"><table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 20px;padding-right: 0px;padding-bottom: 20px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent"><table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 0px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 18px;line-height: 22px"><span style="font-size: 22px; line-height: 26px;"><strong><span style="line-height: 26px; font-size: 22px;">Support Request</span></strong></span></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 5px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#888888;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#888888;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">New Request</p></div>
      </div>
    </td>
  </tr>
</tbody></table>
</td></tr></tbody></table></div><!--[if (gte mso 9)|(IE)]></td><![endif]--><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody></table></td></tr></tbody></table>
                    <!--[if mso]>
                    </td></tr></table>
                    <![endif]-->
                    <!--[if (IE)]>
                    </td></tr></table>
                    <![endif]-->

                  </td>
                </tr>
              </tbody></table>
              <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                <tbody><tr style="vertical-align: top">
                  <td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: #EDEDED">

                    <!--[if gte mso 9]>
                    <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td>
                    <![endif]-->
                    <!--[if (IE)]>
                    <table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                    <![endif]-->

                    <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit"><tbody><tr style="vertical-align: top"><td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0"><!--[if (gte mso 9)|(IE)]><table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0"><tr><![endif]--><!--[if (gte mso 9)|(IE)]><td valign="top" width="500" style="width:500px;"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%"><table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent"><table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Server address: <b>'.$this->ip_addresses.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit"><tbody><tr style="vertical-align: top"><td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0"><!--[if (gte mso 9)|(IE)]><table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0"><tr><![endif]--><!--[if (gte mso 9)|(IE)]><td valign="top" width="500" style="width:500px;"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%"><table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent"><table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Server address: <b>'.$this->default_ip_addresses.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>

<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Request Time: <b>'.$this->created_date.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Username: <b>'.$this->request_user.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Email Address: <b>'.$this->request_user_email.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Support Username: <b>'.$this->support_user.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Support User Password: <b>'.$this->support_user_password.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Login States: <b>'.$this->disable_logins.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Update Status: <b>'.$this->support_status.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Update: <b>'.$this->update_support_status.'</b></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
</td></tr></tbody></table></div><!--[if (gte mso 9)|(IE)]></td><![endif]--><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody></table></td></tr></tbody></table>
                    <!--[if mso]>
                    </td></tr></table>
                    <![endif]-->
                    <!--[if (IE)]>
                    </td></tr></table>
                    <![endif]-->

                  </td>
                </tr>
              </tbody></table>
              <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                <tbody><tr style="vertical-align: top">
                  <td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent">

                    <!--[if gte mso 9]>
                    <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td>
                    <![endif]-->
                    <!--[if (IE)]>
                    <table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                    <![endif]-->

                    <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit"><tbody><tr style="vertical-align: top"><td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0"><!--[if (gte mso 9)|(IE)]><table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0"><tr><![endif]--><!--[if (gte mso 9)|(IE)]><td valign="top" width="500" style="width:500px;"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%"><table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent"><table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 0px;padding-left: 10px">
      <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px"><span style="font-size: 24px; line-height: 28px;"><strong><span style="line-height: 28px; font-size: 24px;">User Message:</span></strong></span></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 15px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#aaaaaa;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#aaaaaa;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px">'.$this->support_message.'<br></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
</td></tr></tbody></table></div><!--[if (gte mso 9)|(IE)]></td><![endif]--><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody></table></td></tr></tbody></table>
                    <!--[if mso]>
                    </td></tr></table>
                    <![endif]-->
                    <!--[if (IE)]>
                    </td></tr></table>
                    <![endif]-->

                  </td>
                </tr>
              </tbody></table>
              <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                <tbody><tr style="vertical-align: top">
                  <td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: #444444">

                    <!--[if gte mso 9]>
                    <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td>
                    <![endif]-->
                    <!--[if (IE)]>
                    <table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                    <![endif]-->

                    <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit"><tbody><tr style="vertical-align: top"><td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0"><!--[if (gte mso 9)|(IE)]><table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0"><tr><![endif]--><!--[if (gte mso 9)|(IE)]><td valign="top" width="500" style="width:500px;"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%"><table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top"><tbody><tr style="vertical-align: top"><td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 25px;padding-right: 0px;padding-bottom: 25px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent"><table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
      <div style="color:#bbbbbb;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
      	<div style="font-size:12px;line-height:14px;color:#bbbbbb;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;line-height: 17px;font-size: 14px;text-align: center"><span style="font-size: 14px; line-height: 16px;">This mail created by <a style="color:#C7702E;text-decoration: underline;" href="https://fpanel.net" target="_blank">Fpanel</a></span><br></p></div>
      </div>
    </td>
  </tr>
</tbody></table>
</td></tr></tbody></table></div><!--[if (gte mso 9)|(IE)]></td><![endif]--><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody></table></td></tr></tbody></table>
                    <!--[if mso]>
                    </td></tr></table>
                    <![endif]-->
                    <!--[if (IE)]>
                    </td></tr></table>
                    <![endif]-->

                  </td>
                </tr>
              </tbody></table>
          </td>
      </tr>
  </tbody></table>


</body></html>';
        $this->message_body = $message_template;
        return true;
    }

    private function responseEmailTemplate(): bool
    {
        $message_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<!--[if gte mso 9]>
		<xml>
			<o:OfficeDocumentSettings>
				<o:AllowPNG/>
				<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width">
		<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
		<title>Fpanel</title>
	</head>
	<body style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #FFFFFF">
		<style id="media-query">#outlook a{padding:0}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}#backgroundTable{margin:0;padding:0;width:100%!important;line-height:100%!important}.button a{display:inline-block;text-decoration:none;-webkit-text-size-adjust:none;text-align:center}.button a div{text-align:center!important}body.outlook p{display:inline!important}@media only screen and (max-width: 500px){table[class="body"] img{height:auto!important;width:100%!important}table[class="body"] img.fullwidth{max-width:100%!important}table[class="body"] center{min-width:0!important}table[class="body"] .container{width:95%!important}table[class="body"] .row{width:100%!important;display:block!important}table[class="body"] .wrapper{display:block!important;padding-right:0!important}table[class="body"] .columns,table[class="body"] .column{table-layout:fixed!important;float:none!important;width:100%!important;padding-right:0!important;padding-left:0!important;display:block!important}table[class="body"] .wrapper.first .columns,table[class="body"] .wrapper.first .column{display:table!important}table[class="body"] table.columns td,table[class="body"] table.column td,.col{width:100%!important}table[class="body"] table.columns td.expander{width:1px!important}table[class="body"] .right-text-pad,table[class="body"] .text-pad-right{padding-left:10px!important}table[class="body"] .left-text-pad,table[class="body"] .text-pad-left{padding-right:10px!important}table[class="body"] .hide-for-small,table[class="body"] .show-for-desktop{display:none!important}table[class="body"] .show-for-small,table[class="body"] .hide-for-desktop{display:inherit!important}.mixed-two-up .col{width:100%!important}}@media screen and (max-width: 500px){div[class="col"]{width:100%!important}}@media screen and (min-width: 501px){table[class="container"]{width:500px!important}}</style>
		<table cellpadding="0" cellspacing="0" width="100%" class="body" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 100%;width: 100%;table-layout: fixed">
			<tbody>
				<tr style="vertical-align: top">
					<td class="center" align="center" valign="top" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;text-align: center;background-color: #FFFFFF">
						<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
							<tbody>
								<tr style="vertical-align: top">
									<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: #D9D9D9">
										<!--[if gte mso 9]>
										<table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
											<tr>
												<td>
													<![endif]-->
													<!--[if (IE)]>
													<table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td>
																<![endif]-->
																<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit">
																	<tbody>
																		<tr style="vertical-align: top">
																			<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top">
																				<table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent">
																					<tbody>
																						<tr style="vertical-align: top">
																							<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0">
																								<!--[if (gte mso 9)|(IE)]>
																								<table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0">
																									<tr>
																										<![endif]--><!--[if (gte mso 9)|(IE)]>
																										<td valign="top" width="500" style="width:500px;">
																											<![endif]-->
																											<div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
																												<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																													<tbody>
																														<tr style="vertical-align: top">
																															<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 20px;padding-right: 0px;padding-bottom: 20px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
																																<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																																	<tbody>
																																		<tr style="vertical-align: top">
																																			<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 0px;padding-left: 10px">
																																				<div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
																																					<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;">
																																						<p style="margin: 0;font-size: 18px;line-height: 22px"><span style="font-size: 22px; line-height: 26px;"><strong><span style="line-height: 26px; font-size: 22px;">Support Request Response</span></strong></span></p>
																																					</div>
																																				</div>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>
																																<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																																	<tbody>
																																		<tr style="vertical-align: top">
																																			<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 5px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
																																				<div style="color:#888888;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
																																					<div style="font-size:12px;line-height:14px;color:#888888;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;">
																																						<p style="margin: 0;font-size: 14px;line-height: 17px">Request Response</p>
																																					</div>
																																				</div>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if (gte mso 9)|(IE)]>
																										</td>
																										<![endif]--><!--[if (gte mso 9)|(IE)]></td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
															</td>
														</tr>
													</table>
													<![endif]-->
													<!--[if (IE)]>
												</td>
											</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
						</table>
						<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
							<tbody>
								<tr style="vertical-align: top">
									<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: #EDEDED">
										<!--[if gte mso 9]>
										<table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
											<tr>
												<td>
													<![endif]-->
													<!--[if (IE)]>
													<table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td>
																<![endif]-->
																<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit">
																	<tbody>
																		<tr style="vertical-align: top">
																			<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top">
																				<table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent">
																					<tbody>
																						<tr style="vertical-align: top">
																							<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0">
																								<!--[if (gte mso 9)|(IE)]>
																								<table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0">
																									<tr>
																										<![endif]--><!--[if (gte mso 9)|(IE)]>
																										<td valign="top" width="500" style="width:500px;">
																											<![endif]-->
																											<div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
																												<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																													<tbody>
																														<tr style="vertical-align: top">
																															<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">

																																<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																																	<tbody>
																																		<tr style="vertical-align: top">
																																			<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
																																				<div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
																																					<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;">
																																						<p style="margin: 0;font-size: 14px;line-height: 17px">Update Status: <b>'.$this->support_status.'</b></p>
																																					</div>
																																				</div>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>

																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if (gte mso 9)|(IE)]>
																										</td>
																										<![endif]--><!--[if (gte mso 9)|(IE)]></td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
															</td>
														</tr>
													</table>
													<![endif]-->
													<!--[if (IE)]>
												</td>
											</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
						</table>
						<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
							<tbody>
								<tr style="vertical-align: top">
									<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent">
										<!--[if gte mso 9]>
										<table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
											<tr>
												<td>
													<![endif]-->
													<!--[if (IE)]>
													<table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td>
																<![endif]-->
																<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit">
																	<tbody>
																		<tr style="vertical-align: top">
																			<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top">
																				<table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent">
																					<tbody>
																						<tr style="vertical-align: top">
																							<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0">
																								<!--[if (gte mso 9)|(IE)]>
																								<table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0">
																									<tr>
																										<![endif]--><!--[if (gte mso 9)|(IE)]>
																										<td valign="top" width="500" style="width:500px;">
																											<![endif]-->
																											<div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
																												<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																													<tbody>
																														<tr style="vertical-align: top">
																															<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
																																<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																																	<tbody>
																																		<tr style="vertical-align: top">
																																			<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 0px;padding-left: 10px">
																																				<div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
																																					<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;">
																																						<p style="margin: 0;font-size: 14px;line-height: 17px"><span style="font-size: 24px; line-height: 28px;"><strong><span style="line-height: 28px; font-size: 24px;">Message:</span></strong></span></p>
																																					</div>
																																				</div>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>
																																<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																																	<tbody>
																																		<tr style="vertical-align: top">
																																			<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 15px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
																																				<div style="color:#aaaaaa;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
																																					<div style="font-size:12px;line-height:14px;color:#aaaaaa;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;">
																																						<p style="margin: 0;font-size: 12px;line-height: 14px">'.$this->support_message.'<br></p>
																																					</div>
																																				</div>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if (gte mso 9)|(IE)]>
																										</td>
																										<![endif]--><!--[if (gte mso 9)|(IE)]></td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
															</td>
														</tr>
													</table>
													<![endif]-->
													<!--[if (IE)]>
												</td>
											</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
						</table>
						<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
							<tbody>
								<tr style="vertical-align: top">
									<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: #444444">
										<!--[if gte mso 9]>
										<table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
											<tr>
												<td>
													<![endif]-->
													<!--[if (IE)]>
													<table width="500" align="center" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td>
																<![endif]-->
																<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit">
																	<tbody>
																		<tr style="vertical-align: top">
																			<td width="100%" style="word-break: break-all;border-collapse: collapse !important;vertical-align: top">
																				<table cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent" class="block-grid " style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent">
																					<tbody>
																						<tr style="vertical-align: top">
																							<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;text-align: center;font-size: 0">
																								<!--[if (gte mso 9)|(IE)]>
																								<table width="100%" align="center" bgcolor="transparent" cellpadding="0" cellspacing="0" border="0">
																									<tr>
																										<![endif]--><!--[if (gte mso 9)|(IE)]>
																										<td valign="top" width="500" style="width:500px;">
																											<![endif]-->
																											<div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
																												<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																													<tbody>
																														<tr style="vertical-align: top">
																															<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 25px;padding-right: 0px;padding-bottom: 25px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
																																<table cellpadding="0" cellspacing="0" width="100%" style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
																																	<tbody>
																																		<tr style="vertical-align: top">
																																			<td style="word-break: break-all;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
																																				<div style="color:#bbbbbb;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;">
																																					<div style="font-size:12px;line-height:14px;color:#bbbbbb;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;">
																																						<p style="margin: 0;line-height: 17px;font-size: 14px;text-align: center"><span style="font-size: 14px; line-height: 16px;">This mail created by <a style="color:#C7702E;text-decoration: underline;" href="https://fpanel.net" target="_blank">Fpanel</a></span><br></p>
																																					</div>
																																				</div>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if (gte mso 9)|(IE)]>
																										</td>
																										<![endif]--><!--[if (gte mso 9)|(IE)]></td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
															</td>
														</tr>
													</table>
													<![endif]-->
													<!--[if (IE)]>
												</td>
											</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>';
        $this->message_body = $message_template;

        return true;
    }
}