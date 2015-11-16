<div style="width:550px;">
    <div class="orderwpa-top">
        <span class="orderwpa-amount">&yen;{$data.order_amount}</span>
        <span class="orderwpa-serial">
            <p>订单状态：<span class="orderstatus {$data.status}">{$data.statusX}</span></p>
            <p>
                {if $data.status eq 'delivering' or $data.status eq 'received'}
                    快递信息：{$data.expressName} &lt;<a href="javascript:;" title="点击查询" onclick="$.fancybox.close();
                            $('#od-exp-view{$data.order_id}').click();">{$data.express_code}</a>&gt;
                {/if}
            </p>
            <p>下单时间：{$data.order_time}</p>
            <p>订单编号：{$data.serial_number} </p>
            <p>微支付号：{$data.wepay_serial}</p>
        </span>
    </div>
    <div class="clearfix">
        {section name=od loop=$data.products}
            <div class='orderwpa-pdlist'>
                <img width="60px" height="60px" src="{$docroot}static/Thumbnail/?w=100&h=100&p={$docroot}uploads/product_hpic/{$data.products[od].catimg}" />
                <div style="margin-left: 70px;height: 60px;line-height: 20px;">
                    <div class="Elipsis">{$data.products[od].product_name}</div>
                    <div style="margin-top:3px;">
                        <i class="opprice">&yen;{$data.products[od].product_discount_price}</i> &times; 
                        <i id="order{$data.order_id}count" class="opcount">{$data.products[od].product_count}</i>
                    </div>
                    <div style="margin-top:0;color:#666;font-size: 12px;">{if $data.products[od].det_name1 neq ''}[{$data.products[od].det_name1}{$data.products[od].det_name2}]{else}[默认规格]{/if}</div>
                </div>
            </div>
        {/section}
    </div>
    <div class="orderwpa-address clearfix" style="">
        <p>姓名：{$data.address.user_name}</p>
        <p>电话：{$data.address.tel_number}</p>
        <p>地址：{$data.address.address}</p>
        <p>邮编：{$data.address.postal_code}</p>
        <p>配送：{$data.exptime}</p>
        <p>备注：{$data.leword}</p>
        {if $data.reci_tex > 0}
            <p>发票抬头：{$data.reci_head}</p>
            <p>发票内容：{$data.reci_cont}</p>
            <p>发票税额：&yen;{$data.reci_tex}</p>
        {/if}
    </div>

    <!-- 未发货 -->
    {if $data.status eq 'payed'}
        <div style="text-align: center;padding-top:10px;" class='clearfix'>
            <input type="text" id='despatchExpressCode' value="" placeholder="请填写快递单号"/>
            <select id="expressCompany">
                {foreach from=$expressCompany key=myId item=i}
                    <option value="{$myId}">{$i}</option>
                {/foreach}
            </select>
            <select id="expressStaff">
                <option value="">无配送人员</option>
                {foreach from=$expressStaff item=i}
                    <option value="{$i.client_wechat_openid}">{$i.client_name}</option>
                {/foreach}
            </select>
        </div>
        <div style="text-align: center;margin-top: 10px;">
            <a class="wd-btn primary" id='despatchBtn' href='javascript:;' data-orderid="{$data.order_id}">确认发货</a>
        </div>
    {/if}

    {if $data.status eq 'delivering'}
        <div style="text-align: center;margin-top: 10px;">
            <a class="wd-btn primary" href='javascript:;' onclick="util.confirmExp({$data.order_id});">确认收货</a>
        </div>
    {/if}
</div>