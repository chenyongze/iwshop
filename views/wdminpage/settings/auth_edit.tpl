<form style="width:350px;padding:5px 10px;">

    <div class="gs-label">账号</div>
    <div class="gs-text">
        <input type="text" id="acc" value="{$auth.admin_account}" autocomplete="off"/>
    </div>    

    <div class="gs-label">密码</div>
    <div class="gs-text">
        <input type="password" id="pwd" value="" autocomplete="off" />
    </div>

    <div class="gs-label">权限</div>

    <div id="authList" class="expprovince clearfix" style="display: block;width: 356px;">
        <a class="expitem sm {if $auth.arr.stat}hov{/if}" data-auth="stat" href="javascript:;">报表中心</a>
        <a class="expitem sm {if $auth.arr.orde}hov{/if}" data-auth="orde" href="javascript:;">订单管理</a>
        <a class="expitem sm {if $auth.arr.prod}hov{/if}" data-auth="prod" href="javascript:;">商品管理</a>
        <a class="expitem sm {if $auth.arr.gmes}hov{/if}" data-auth="gmes" href="javascript:;">消息群发</a>
        <a class="expitem sm {if $auth.arr.user}hov{/if}" data-auth="user" href="javascript:;">会员管理</a>
        <a class="expitem sm {if $auth.arr.comp}hov{/if}" data-auth="comp" href="javascript:;">代理合作</a>
        <a class="expitem sm {if $auth.arr.sett}hov{/if}" data-auth="sett" href="javascript:;">微店设置</a>
    </div>

</form>

<div class="center" style="margin:0 -15px;">
    <a class="wd-btn primary" style="width:150px" id="al-com-save" data-id="{$auth.id}" href="javascript:;">
        {if $com.id > 0}提交{else}保存{/if}
    </a>
</div>
