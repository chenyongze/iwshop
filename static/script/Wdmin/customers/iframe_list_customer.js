
requirejs(['jquery', 'util', 'datatables', 'pagination', 'baiduTemplate'], function ($, util, dataTables, pagination, baiduTemplate) {

    $(function () {

        var param = {
            gid: $('#gid').val(),
            page: 0
        }

        if (parent.frameOnload !== undefined) {
            parent.frameOnload();
        }

        /**
         * 加载列表
         * @returns {void}
         */
        function fnLoadList() {
            var params = $.param(param);
            // [HttpGet]
            $.get('?/WdminPage/ajax_list_customerData/' + params, function (json) {
                var html = baidu.template('t:ct_list', {
                    list: json,
                    shoproot: shoproot
                });
                $('.dTable tbody').empty().html(html);
                //util.imageError();
                util.dataTableLis();
            });
        }

        fnLoadList();

        //分页初始化
        $('.pagination-sm').twbsPagination({
            first: '首页',
            prev: '前页',
            next: '后页',
            last: '尾页',
            totalPages: 100,
            visiblepages: 6,
            onPageClick: function (event, page) {
                param.page = page - 1;
                fnLoadList();
            }
        });

    });

});