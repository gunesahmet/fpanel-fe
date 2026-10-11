<form name="chmod">
    <table class="table table-bordered" border="0" cellspacing="0" cellpadding="0" style="font:normal 12px verdana" ;>
        <tr align="left" valign="middle">
            <td><?= __('Permissions')/*İzinler*/;?></td>
            <td><input type="text" name="t_total" value="751" size="4" class="form-control form-control-sm"></td>
            <td><input type="text" name="sym_total" value="" size="12" disabled class="form-control form-control-sm"></td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered" cellpadding="2" cellspacing="0" border="0">
        <thead class="thead-dark text-center">
            <tr>
                <th width="60" align="left"></th>
                <th width="55"><b>owner</b></th>
                <th width="55"><b>group</b></th>
                <th width="55"><b>other<b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="60" align="left" nowrap bgcolor="#ffffff">read</td>
                <td width="55" align="center" bgcolor="#eeeeee">
                    <input class="checkbox-blue" type="checkbox" name="owner4" value="4">
                </td>
                <td width="55" align="center" bgcolor="#ffffff">
                    <input class="checkbox-blue" type="checkbox" name="group4" value="4">
                </td>
                <td width="55" align="center" bgcolor="#eeeeee">
                    <input class="checkbox-blue" type="checkbox" name="other4" value="4">
                </td>
            </tr>
            <tr>
                <td width="60" align="left" nowrap bgcolor="#ffffff">write</td>
                <td width="55" align="center" bgcolor="#eeeeee">
                    <input class="checkbox-blue" type="checkbox" name="owner2" value="2">
                </td>
                <td width="55" align="center" bgcolor="#ffffff">
                    <input class="checkbox-blue" type="checkbox" name="group2" value="2">
                </td>
                <td width="55" align="center" bgcolor="#eeeeee">
                    <input class="checkbox-blue" type="checkbox" name="other2" value="2">
                </td>
            </tr>
            <tr>
                <td width="60" align="left" nowrap bgcolor="#ffffff">execute</td>
                <td width="55" align="center" bgcolor="#eeeeee">
                    <input class="checkbox-blue" type="checkbox" name="owner1" value="1">
                </td>
                <td width="55" align="center" bgcolor="#ffffff">
                    <input class="checkbox-blue" type="checkbox" name="group1" value="1">
                </td>
                <td width="55" align="center" bgcolor="#eeeeee">
                    <input class="checkbox-blue" type="checkbox" name="other1" value="1">
                </td>
            </tr>
        </tbody>
    </table>
</form>

