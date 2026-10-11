<form name="chmod">
    <table class="table table-bordered" border="0" cellspacing="0" cellpadding="0" style="font:normal 12px verdana" ;>
        <tr align="left" valign="middle">
            <td><? echo __('Permissions')/*İzinler*/;?>: </td>
            <td><input type="text" name="t_total" value="751" size="4"> </td>
            <td><input type="text" name="sym_total" value="" size="12" readonly="1" style='border: 0px none; font-family: "courier new", courier, mono;'></td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered" cellpadding="2" cellspacing="0" border="0" style="font:normal 12px verdana">
        <tr bgcolor="#333333">
            <td width="60" align="left"> </td>
            <td width="55" align="center" style="color:white"><b>owner
                </b></td>
            <td width="55" align="center" style="color:white"><b>group
                </b></td>
            <td width="55" align="center" style="color:white"><b>other
                    <b></td>
        </tr>
        <tr bgcolor="#dddddd">
            <td width="60" align="left" nowrap bgcolor="#ffffff">read</td>
            <td width="55" align="center" bgcolor="#eeeeee">
                <input class="checkbox" type="checkbox" name="owner4" value="4">
            </td>
            <td width="55" align="center" bgcolor="#ffffff">
                <input class="checkbox" type="checkbox" name="group4" value="4">
            </td>
            <td width="55" align="center" bgcolor="#eeeeee">
                <input class="checkbox" type="checkbox" name="other4" value="4">
            </td>
        </tr>
        <tr bgcolor="#dddddd">
            <td width="60" align="left" nowrap bgcolor="#ffffff">write</td>
            <td width="55" align="center" bgcolor="#eeeeee">
                <input class="checkbox" type="checkbox" name="owner2" value="2">
            </td>
            <td width="55" align="center" bgcolor="#ffffff">
                <input type="checkbox" name="group2" value="2">
            </td>
            <td width="55" align="center" bgcolor="#eeeeee">
                <input class="checkbox" type="checkbox" name="other2" value="2">
            </td>
        </tr>
        <tr bgcolor="#dddddd">
            <td width="60" align="left" nowrap bgcolor="#ffffff">execute</td>
            <td width="55" align="center" bgcolor="#eeeeee">
                <input class="checkbox" type="checkbox" name="owner1" value="1">
            </td>
            <td width="55" align="center" bgcolor="#ffffff">
                <input class="checkbox" type="checkbox" name="group1" value="1">
            </td>
            <td width="55" align="center" bgcolor="#eeeeee">
                <input class="checkbox" type="checkbox" name="other1" value="1">
            </td>
        </tr>
    </table>

</form>

