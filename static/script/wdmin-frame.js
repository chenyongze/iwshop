

var scriptTag = document.getElementById('scriptTag');

var shoproot = location.pathname.substr(0, location.pathname.lastIndexOf('/') + 1);

window.UMEDITOR_HOME_URL = shoproot + 'static/script/umeditor/';

var shopdomain = location.hostname;

// datatable 配置项
var DataTableConfig = {
    "bPaginate": false,
    "bLengthChange": false,
    "iDisplayLength": 6000,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false,
    "fnInitComplete": function () {
        dataTableLis();
        $('.dataTables_filter').addClass('clearfix');
        $('.search-w-box input').attr('placeholder', '输入搜索内容');
    }
};

if (scriptTag) {
    require.config({
        packages: [
            {
                name: 'echarts',
                location: './Echarts/src',
                main: 'echarts'
            },
            {
                name: 'zrender',
                location: './zrender/src', // zrender与echarts在同一级目录
                main: 'zrender'
            }
        ]
    });
    require.config({
        paths: {
            jquery: 'http://cdns.ycchen.cc/scripts/jquery-1.7.2.ext.min',
            bootstrap: 'http://cdns.ycchen.cc/scripts/bootstrap.min',
            util: 'Wdmin/util',
            Spinner: 'http://cdns.ycchen.cc/scripts/spin.min',
            highcharts: 'http://cdns.ycchen.cc/scripts/highcharts',
            fancyBox: 'fancyBox/source/jquery.fancybox.pack',
            datatables: 'DataTables/media/js/jquery.dataTables.min',
            jqPaginator: 'http://cdns.ycchen.cc/scripts/jqPaginator',
            provinceCity: 'lib/provinceCity',
            jUploader: 'jUploader.min',
            ztree: 'zTree_v3/js/jquery.ztree.core-3.5.min',
            ztree_loader: 'Wdmin/ztree_loader',
            ueditor: 'umeditor/umeditor.min',
            ueditor_config: 'umeditor/umeditor.config',
            pagination: 'http://cdns.ycchen.cc/scripts/jquery.pagination.min',
            datetimepicker: 'http://cdns.ycchen.cc/scripts/jquery.datetimepicker.full.min',
            baiduTemplate: 'http://cdns.ycchen.cc/scripts/baiduTemplate',
            'jquery-mousewheel': 'http://cdns.ycchen.cc/scripts/jquery-mousewheel',
            page_orders_all: 'Wdmin/orders/orders_all',
            page_orders_toexpress: 'Wdmin/orders/orders_toexpress',
            page_orders_expressing: 'Wdmin/orders/orders_expressing',
            page_orders_toreturn: 'Wdmin/orders/orders_toreturn',
            page_home: 'Wdmin/stat_center/home',
            page_list_products: 'Wdmin/products/list_products',
            page_alter_products_categroy: 'Wdmin/products/alter_products_categroy',
            page_alter_categroy: 'Wdmin/products/alter_categroy',
            page_iframe_list_products: 'Wdmin/products/iframe_list_products',
            page_iframe_alter_product: 'Wdmin/products/iframe_alter_product',
            page_list_customers: 'Wdmin/customers/list_customers',
            page_deleted_products: 'Wdmin/products/deleted_products',
            page_alter_product_specs: 'Wdmin/products/alter_product_specs',
            page_list_customer_orders: 'Wdmin/customers/list_customer_orders',
            page_list_companys: 'Wdmin/company/list_companys',
            page_company_requests: 'Wdmin/company/company_requests',
            page_alter_product_serials: 'Wdmin/products/alter_product_serials',
            page_alter_products_brand: 'Wdmin/products/page_alter_products_brand',
            // services
            user: 'Wdmin/service/user-service',
            product: 'Wdmin/service/product-service',
            setting: 'Wdmin/service/setting-service',
            order: 'Wdmin/service/order-service',
            supplier: 'Wdmin/service/supplier-service'
        },
        shim: {
            'pagination': {
                deps: ['jquery']
            },
            'page_home': {
                deps: ['jquery', 'highcharts']
            },
            'page_orders_all': {
                deps: ['jquery', 'datatables']
            },
            'page_orders_toexpress': {
                deps: ['jquery', 'datatables']
            },
            'page_orders_expressing': {
                deps: ['jquery', 'datatables']
            },
            'page_orders_toreturn': {
                deps: ['jquery', 'datatables']
            },
            'page_list_products': {
                deps: ['jquery', 'datatables']
            },
            'page_alter_products_categroy': {
                deps: ['jquery', 'datatables', 'ztree']
            },
            'page_alter_categroy': {
                deps: ['jquery', 'datatables', 'ztree', 'jUploader']
            },
            'page_iframe_list_products': {
                deps: ['jquery', 'datatables', 'ztree']
            },
            'page_iframe_alter_product': {
                deps: ['jquery', 'datatables', 'ztree', 'ueditor', 'jUploader']
            },
            'page_alter_products_brand': {
                deps: ['jquery']
            },
            'page_list_customers': {
                deps: ['jquery', 'datatables', 'ztree', 'ueditor']
            },
            'page_deleted_products': {
                deps: ['jquery', 'datatables', 'ztree', 'ueditor']
            },
            'page_alter_product_specs': {
                deps: ['jquery']
            },
            'page_list_customer_orders': {
                deps: ['jquery']
            },
            'page_list_companys': {
                deps: ['jquery']
            },
            'page_company_requests': {
                deps: ['jquery']
            },
            'page_alter_product_serials': {
                deps: ['jquery']
            },
            'fancyBox': {
                deps: ['jquery']
            },
            'jUploader': {
                deps: ['jquery']
            },
            'datetimepicker': {
                deps: ['jquery'],
                exports: 'datetimepicker'
            },
            'datatables': {
                deps: ['jquery'],
                exports: 'datatable'
            },
            'provinceCity': {
                deps: ['jquery'],
                exports: 'provinceCity'
            },
            'supplier': {
                deps: ['jquery'],
                exports: 'supplier'
            },
            'highcharts': {
                deps: ['jquery'],
                exports: 'highcharts'
            },
            'ztree_loader': {
                deps: ['ztree', 'jquery'],
                exports: 'ztree_loader'
            },
            'ueditor': {
                deps: ['jquery', 'ueditor_config']
            },
            'ztree': {
                deps: ['jquery']
            },
            'bootstrap': {
                deps: ['jquery']
            },
            'jquery': {
                exports: '$'
            }
        },
        //urlArgs: "bust=1.5.3",
        urlArgs: "bust=" + (new Date()).getMonth().toString() + (new Date()).getDay().toString() + (new Date()).getHours().toString(),
        xhtml: true
    });

    require([scriptTag.innerHTML]);
}