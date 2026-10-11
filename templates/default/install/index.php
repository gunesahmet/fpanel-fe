<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <?= __('Software License Agreement');?>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-left">
                            <a href="<?= $this->uri->encode("logout", true);?>" class="btn btn-danger btn-icon" type="submit"> <i class="fa fa-times-circle-o"></i> <?= __('I don\'t Agree');?> </a>
                            <a href="<?= $this->uri->encode('install/serverinfo');?>" class="btn btn-primary" type="submit"> <i class="fa fa-check-circle-o"></i> <?= __('Read & Accept');?> </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table>
                            <tbody>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td>

                                    <ol>
                                        <li style="list-style-type: none"><br><strong><span style="text-decoration: underline;"><?= __('License');?></span></strong><br></li>
                                        <li value=1><?= __('Under this Software License Agreement (the "Agreement"), Fpanel (the "Vendor") grants to the user (the "License") a non-exclusive and non-transferable license (the "License") to use FPanel (the "Software").');?><br><br></li>
                                        <li><?= __('"Software" includes the executable computer programs and any related printed, electronic and online documentation and any other files that may accompany the product.');?><br><br></li>
                                        <li><?= __('Title, copyright, intellectual property rights and distribution rights of the Software remain exclusively with the Vendor. Intellectual property rights include the look and feel of the Software. This Agreement constitutes a license for use only and is not in any way a transfer of ownership rights to the Software.');?><br><br></li>
                                        <li><?= __('The Software may be loaded onto no more than one server. A single copy may be made for backup purposes only.');?><br><br></li>
                                        <li><?= __('The rights and obligations of this Agreement are personal rights granted to the Licensee only. The Licensee may not transfer or assign any of the rights or obligations granted under this Agreement to any other person or legal entity. The Licensee may not make available the Software for use by one or more third parties.');?><br><br></li>
                                        <li><?= __('The Software may not be modified, reverse-engineered, or de-compiled in any manner through current or future available technologies.');?><br><br></li>
                                        <li><?= __('Failure to comply with any of the terms under the License section will be considered a material breach of this Agreement.');?><br><br></li>
                                        <li style="list-style-type: none"><strong><span style="text-decoration: underline;"><?= __('License Fee');?></span></strong><br></li>
                                        <li value=8><?= __('The original purchase price paid by the Licensee will constitute the entire license fee and is the full consideration for this Agreement.');?><br><br></li>
                                    </ol>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <div><?= __('The remainder of this document will be available when you have purchased a license.');?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
