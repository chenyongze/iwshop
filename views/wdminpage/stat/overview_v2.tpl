{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/stat/overview.js</i>
<div class="cleafix">
    <div id="ovw-right">
        <div id="right_charts">
            <div id="right_charts1"></div>
            <div id="right_charts2"></div>
        </div>
    </div>
    <div id="ovw-left">
        <!-- 本月新增订单 -->
        <input type="hidden" id="neworder_month" value="{$Datas.neworder_month}" />
        <!-- 本月新增有效订单 -->
        <input type="hidden" id="valorder_month" value="{$Datas.valorder_month}" />
        <div id="dTb">
            <table class="ovw-table">
                <tr>
                    <td>
                        <span>{$Datas.newfans}<b>人</b></span>
                        <span>新增关注</span>
                    </td>
                    <td>
                        <span>{$Datas.runfans}<b>人</b></span>
                        <span>取消关注</span>
                    </td>
                    <td>
                        <span>{$Datas.newfans - $Datas.runfans}<b>人</b></span>
                        <span>净增关注</span>
                    </td>
                    <td>
                        <span class="green"><i id="allfanscount">{$Datas.allfans}</i><b>人</b></span>
                        <span>总粉丝</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>{$Datas.newuser}<b>人</b></span>
                        <span>新增会员</span>
                    </td>
                    <td class="clickable" onclick="$('#__uslist:eq(0)', parent.document).get(0).click();">
                        <span><i id="usersum">{$Datas.alluser}</i><b>人</b></span>
                        <span>会员总数</span>
                    </td>
                    <td>
                        <span>{$Datas.newcoms}<b>人</b></span>
                        <span>新增代理</span>
                    </td>
                    <td class="clickable" onclick="$('#__comlist:eq(0)', parent.document).get(0).click();">
                        <span class="green"><i id="comsum">{$Datas.allcoms}</i><b>人</b></span>
                        <span>代理总数</span>
                    </td>
                </tr>
            </table>
            <table class="ovw-table">
                <tr>
                    <td>
                        <span class="pricesx">&yen;{$Datas.saletoday}<b>元</b></span>
                        <span>今日成交</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.saleyestoday}<b>元</b></span>
                        <span>昨日成交</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.salemonth}<b>元</b></span>
                        <span>本月成交</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.saletotal}<b>元</b></span>
                        <span>历史成交</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="pricesx">&yen;{$Datas.pxsaletoday}<b>元</b></span>
                        <span>今日代理成交</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.pxsaleyestoday}<b>元</b></span>
                        <span>本周代理成交</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.pxsalemonth}<b>元</b></span>
                        <span>本月代理成交</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.pxsaletotal}<b>元</b></span>
                        <span>代理历史成交</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="green">{$Datas.neworder}<b>笔</b></span>
                        <span>今日新增订单</span>
                    </td>
                    <td>
                        <span>{$Datas.neworderyes}<b>笔</b></span>
                        <span>昨日新增订单</span>
                    </td>
                    <td>
                        <span class="green">{$Datas.neworderpayed}<b>笔</b></span>
                        <span>今日已付款订单</span>
                    </td>
                    <td>
                        <span>{$Datas.neworderpayedyes}<b>笔</b></span>
                        <span>昨日已付款订单</span>
                    </td>
                </tr>
                <tr>
                    <td class="clickable" onclick="$('#__odpaylist:eq(0)', parent.document).get(0).click();">
                        <span class="green"><i id="ordertoexp">{$Datas.orderpayed}</i><b>笔</b></span>
                        <span>未发货订单</span>
                    </td>
                    <td class="clickable" onclick="$('#__oddevlist:eq(0)', parent.document).get(0).click();">
                        <span class="green"><i id="orderdelivering">{$Datas.orderexped}</i><b>笔</b></span>
                        <span>已发货订单</span>
                    </td>
                    <td class="clickable" onclick="$('#__odcanlist:eq(0)', parent.document).get(0).click();">
                        <span class="pricesx">{$Datas.ordercanceled}<b>笔</b></span>
                        <span>退货申请</span>
                    </td>
                    <td class="clickable" onclick="$('#__odalllist:eq(0)', parent.document).get(0).click();">
                        <span>{$Datas.ordermonth}<b>笔</b></span>
                        <span>本月订单</span>
                    </td>
                </tr>
                <tr>
                    <td class="clickable" onclick="$('#__catlist:eq(0)', parent.document).get(0).click();">
                        <span>{$Datas.catotal}<b>个</b></span>
                        <span>商品分类</span>
                    </td>
                    <td class="clickable" onclick="$('#__pdlist:eq(0)', parent.document).get(0).click();">
                        <span>{$Datas.pdtotal}<b>种</b></span>
                        <span>商品总数</span>
                    </td>
                    <td>
                        <span>{$Datas.pdtotalavg}<b>次</b></span>
                        <span>平均商品浏览</span>
                    </td>
                    <td>
                        <span class="pricesx">&yen;{$Datas.pdpriceavg}<b>元</b></span>
                        <span>商品平均价格</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
{include file='../__footer.tpl'} 