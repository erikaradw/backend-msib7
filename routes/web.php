<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\stm;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
// use Maatwebsite\Excel\Facades\Excel;
// use App\Imports\ImportExcel;

// $router->post('/import', function () {
//     // Validasi apakah file diunggah dengan benar
//     $file = request()->file('stms');

//     if ($file) {
//         // Import data dari file Excel
//         try {
//             Excel::import(new ImportExcel, $file);
//             return response()->json(['message' => 'Users imported successfully!'], 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => 'Error importing file: ' . $e->getMessage()], 500);
//         }
//     }

//     return response()->json(['error' => 'No file uploaded!'], 400);
// });

// use App\Http\Controllers\ExcelController;

// $router->post('/import-excel', [ExcelController::class, 'importExcel']);



$router->get('/', function () use ($router) {
    return $router->app->version();
});

// $router->get('/test', 'KaryawanController@test');
// $router->post('/test', 'KaryawanController@test2');

// $router->get('/karyawan', 'KaryawanController@index');
// $router->post('/karyawan', 'KaryawanController@store');
// $router->delete('/karyawan/{id}', 'KaryawanController@destroy');
// $router->get('/dept', 'MDepartementController@index');


// {baseurl}/prefix/...
$router->group(['prefix' => 'si'], function () use ($router) {
    //api m_price erika
    $router->get('/M_Price', ['uses' => 'M_PriceController@paging']);
    $router->post('/M_Price', ['uses' => 'M_PriceController@store']);
    $router->put('/M_Price/{id}', ['uses' => 'M_PriceController@update']);
    $router->get('/M_Price/{id}', ['uses' => 'M_PriceController@show']);
    $router->delete('/M_Price/{id}', ['uses' => 'M_PriceController@destroy']);
    $router->delete('M_Pricedelete', 'M_PriceController@deleteAll');

    //m_item_price_history
    $router->get('/MItempricehistory', ['uses' => 'MItempricehistoryController@paging']);
    $router->post('/MItempricehistory', ['uses' => 'MItempricehistoryController@store']);
    $router->delete('/MItempricehistory/{id}', ['uses' => 'MItempricehistoryController@destroy']);
    $router->get('/MItempricehistory/{id}', ['uses' => 'MItempricehistoryController@show']);
    $router->put('/MItempricehistory/{id}', ['uses' => 'MItempricehistoryController@update']);
    $router->delete('M_Itempricehistorydelete', 'MItempricehistoryController@deleteAll');

    // ROUTES PO CUSTOMER
    $router->get('pocust', 'POCustController@paging');
    $router->post('pocust', 'POCustController@store');
    $router->delete('pocust/{id}', 'POCustController@destroy');
    $router->get('pocust/{id}', 'POCustController@show');
    $router->put('pocust/{id}', 'POCustController@update');
    $router->delete('pocustdelete', 'POCustController@deleteAll');
    $router->post('pocustinsertBulk', 'POCustController@insertBulk');

    // ROUTES STOCK DETAIL
    $router->get('stockdetail', 'StockDetailController@paging');
    $router->post('stockdetail', 'StockDetailController@store');
    $router->delete('stockdetail/{id}', 'StockDetailController@destroy');
    $router->get('stockdetail/{id}', 'StockDetailController@show');
    $router->put('stockdetail/{id}', 'StockDetailController@update');
    $router->delete('stockdetaildelete', 'StockDetailController@deleteAll');
    $router->post('stockdetailinsertBulk', 'StockDetailController@insertBulk');

    // ROUTES SALES UNIT
    $router->get('salesunit', 'SalesUnitController@paging');
    $router->post('salesunit', 'SalesUnitController@store');
    $router->delete('salesunit/{id}', 'SalesUnitController@destroy');
    $router->get('salesunit/{id}', 'SalesUnitController@show');
    $router->put('salesunit/{id}', 'SalesUnitController@update');
    $router->delete('salesunitdelete', 'SalesUnitController@deleteAll');
    $router->post('salesunitinsertBulk', 'SalesUnitController@insertBulk');

    // ROUTES TREND
    $router->get('trend', 'TrendController@paging');
    $router->post('trend', 'TrendController@store');
    $router->delete('trend/{id}', 'TrendController@destroy');
    $router->get('trend/{id}', 'TrendController@show');
    $router->put('trend/{id}', 'TrendController@update');
    $router->get('trendSelectData', ['uses' => 'TrendController@getSelectData']);
    $router->get('/monthly-sales-data', ['uses' => 'TrendController@getMonthlySalesData']);
    $router->delete('trenddelete', 'TrendController@deleteAll');
    $router->get('/insert-trends-data', 'TrendController@insertTrendsData');

    $router->get('trend_g', 'TrendGController@paging');
    $router->post('trend_g', 'TrendGController@store');
    $router->delete('trend_g/{id}', 'TrendGController@destroy');
    $router->get('trend_g/{id}', 'TrendGController@show');
    $router->put('trend_g/{id}', 'TrendGController@update');
    $router->get('trend_gSelectData', ['uses' => 'TrendGController@getSelectData']);
    $router->get('/monthly-sales-trend-g', ['uses' => 'TrendGController@getMonthlySalesTrendG']);
    $router->delete('trenddelete_g', 'TrendGController@deleteAll');
    $router->get('/insert-trendsg', 'TrendGController@insertTrendsg');

    // API UNTUK table m__products
    $router->get('/M_Product', ['uses' => 'MProductController@paging']);
    $router->post('/M_Product', ['uses' => 'MProductController@store']);
    $router->put('/M_Product/{id}', ['uses' => 'MProductController@update']);
    $router->get('/M_Product/{id}', ['uses' => 'MProductController@show']);
    $router->delete('/M_Product/{id}', ['uses' => 'MProductController@destroy']);
    $router->delete('M_Productdelete', 'MProductController@deleteAll');
    $router->post('M_ProductinsertBulk', 'MProductController@insertBulk');

    // API UNTUK table m__brands
    $router->get('/M_Brand', ['uses' => 'MBrandController@paging']);
    $router->post('/M_Brand', ['uses' => 'MBrandController@store']);
    $router->put('/M_Brand/{id}', ['uses' => 'MBrandController@update']);
    $router->get('/M_Brand/{id}', ['uses' => 'MBrandController@show']);
    $router->delete('/M_Brand/{id}', ['uses' => 'MBrandController@destroy']);
    $router->delete('M_Branddelete', 'MBrandController@deleteAll');

    // API UNTUK table m__areas
    $router->get('/M_Area', ['uses' => 'MAreaController@paging']);
    $router->post('/M_Area', ['uses' => 'MAreaController@store']);
    $router->put('/M_Area/{id}', ['uses' => 'MAreaController@update']);
    $router->get('/M_Area/{id}', ['uses' => 'MAreaController@show']);
    $router->delete('/M_Area/{id}', ['uses' => 'MAreaController@destroy']);
    $router->delete('M_Areadelete', 'MAreaController@deleteAll');

    // API UNTUK table m__regions
    $router->get('/M_Region', ['uses' => 'MRegionController@paging']);
    $router->post('/M_Region', ['uses' => 'MRegionController@store']);
    $router->put('/M_Region/{id}', ['uses' => 'MRegionController@update']);
    $router->get('/M_Region/{id}', ['uses' => 'MRegionController@show']);
    $router->delete('/M_Region/{id}', ['uses' => 'MRegionController@destroy']);
    $router->delete('M_Regiondelete', 'MRegionController@deleteAll');

    // API UNTUK table m__cabangs
    $router->get('/M_Cabang', ['uses' => 'MCabangController@paging']);
    $router->post('/M_Cabang', ['uses' => 'MCabangController@store']);
    $router->put('/M_Cabang/{id}', ['uses' => 'MCabangController@update']);
    $router->get('/M_Cabang/{id}', ['uses' => 'MCabangController@show']);
    $router->delete('/M_Cabang/{id}', ['uses' => 'MCabangController@destroy']);
    $router->delete('M_Cabangdelete', 'MCabangController@deleteAll');

    // API UNTUK table m__opsi_cabangs
    $router->get('/M_OpsiCabang', ['uses' => 'MOpsiCabangController@paging']);
    $router->post('/M_OpsiCabang', ['uses' => 'MOpsiCabangController@store']);
    $router->put('/M_OpsiCabang/{id}', ['uses' => 'MOpsiCabangController@update']);
    $router->get('/M_OpsiCabang/{id}', ['uses' => 'MOpsiCabangController@show']);
    $router->delete('/M_OpsiCabang/{id}', ['uses' => 'MOpsiCabangController@destroy']);
    $router->delete('M_OpsiCabangdelete', 'MOpsiCabangController@deleteAll');

    // API UNTUK table m__customers
    $router->get('/M_Customer', ['uses' => 'MCustomerController@paging']);
    $router->post('/M_Customer', ['uses' => 'MCustomerController@store']);
    $router->put('/M_Customer/{id}', ['uses' => 'MCustomerController@update']);
    $router->get('/M_Customer/{id}', ['uses' => 'MCustomerController@show']);
    $router->delete('/M_Customer/{id}', ['uses' => 'MCustomerController@destroy']);
    $router->post('M_CustomerBulky', 'MCustomerController@storeBulky');
    $router->delete('M_Customerdelete', 'MCustomerController@deleteAll');

    // API UNTUK table m__kategoris
    $router->get('/M_Kategori', ['uses' => 'MKategoriController@paging']);
    $router->post('/M_Kategori', ['uses' => 'MKategoriController@store']);
    $router->put('/M_Kategori/{id}', ['uses' => 'MKategoriController@update']);
    $router->get('/M_Kategori/{id}', ['uses' => 'MKategoriController@show']);
    $router->delete('/M_Kategori/{id}', ['uses' => 'MKategoriController@destroy']);
    $router->post('M_KategoriBulky', 'MKategoriController@storeBulky');
    $router->delete('M_Kategoridelete', 'MKategoriController@deleteAll');
    $router->post('M_KategoriinsertBulk', 'MKategoriController@insertBulk');

    // $router->get('user', 'MUserController@getData');
    // $router->get('user/{id}', 'MUserController@show');
    // $router->post('user', 'MUserController@store');
    // $router->put('user/{id}', 'MUserController@update');
    // $router->delete('user/{id}', 'MUserController@destroy');

    // $router->get('/MMUser', ['uses' => 'MMUserController@paging']);
    // $router->get('/MMUser/{id}', ['uses' => 'MMUserController@show']);
    // $router->post('/MMUser', ['uses' => 'MMUserController@store']);
    // $router->put('/MMUser/{id}', ['uses' => 'MMUserController@update']);
    // $router->delete('/MMUser/{id}', ['uses' => 'MMUserController@destroy']);

    // $router->get('/MTonaseTypeAll', ['uses' => 'MTonaseTypeController@index']);
    // $router->get('/MTonaseType', ['uses' => 'MTonaseTypeController@paging']);
    // $router->get('/MTonaseType/{id}', ['uses' => 'MTonaseTypeController@show']);
    // $router->post('/MTonaseType', ['uses' => 'MTonaseTypeController@store']);
    // $router->put('/MTonaseType/{id}', ['uses' => 'MTonaseTypeController@update']);
    // $router->delete('/MTonaseType/{id}', ['uses' => 'MTonaseTypeController@destroy']);

    // $router->get('/MTonaseUnitAll', ['uses' => 'MTonaseUnitController@index']);
    // $router->get('/MTonaseUnit', ['uses' => 'MTonaseUnitController@paging']);
    // $router->get('/MTonaseUnit/{id}', ['uses' => 'MTonaseUnitController@show']);
    // $router->post('/MTonaseUnit', ['uses' => 'MTonaseUnitController@store']);
    // $router->put('/MTonaseUnit/{id}', ['uses' => 'MTonaseUnitController@update']);
    // $router->delete('/MTonaseUnit/{id}', ['uses' => 'MTonaseUnitController@destroy']);

    // $router->get('/MItemTonaseOpt', ['uses' => 'MItemTonaseOptController@paging']);
    // $router->get('/MItemTonaseOpt/{id}', ['uses' => 'MItemTonaseOptController@show']);
    // $router->post('/MItemTonaseOpt', ['uses' => 'MItemTonaseOptController@store']);
    // $router->put('/MItemTonaseOpt/{id}', ['uses' => 'MItemTonaseOptController@update']);
    // $router->delete('/MItemTonaseOpt/{id}', ['uses' => 'MItemTonaseOptController@destroy']);

    // //api m_discount erika
    // $router->get('/M_Discount', ['uses' => 'M_DiscountController@paging']);
    // $router->post('/M_Discount', ['uses' => 'M_DiscountController@store']);
    // $router->put('/M_Discount/{id}', ['uses' => 'M_DiscountController@update']);
    // $router->get('/M_Discount/{id}', ['uses' => 'M_DiscountController@show']);
    // $router->delete('/M_Discount/{id}', ['uses' => 'M_DiscountController@destroy']);

    // // farizz
    // $router->get('/MItemOptInfo', ['uses' => 'MItemOptInfoController@paging']);
    // $router->get('/MItemOptInfo/{id}', ['uses' => 'MItemOptInfoController@showData']);
    // $router->post('/MItemOptInfo', ['uses' => 'MItemOptInfoController@storeData']);
    // $router->put('/MItemOptInfo/{id}', ['uses' => 'MItemOptInfoController@updateData']);
    // $router->delete('/MItemOptInfo/{id}', ['uses' => 'MItemOptInfoController@destroyData']);

    // $router->get('/MItemOptDetail/OptDetailCodes', ['uses' => 'MItemOptDetailController@getAllOptDetailCodeOnly']);
    // $router->get('/MItemOptDetail', ['uses' => 'MItemOptDetailController@paging']);
    // $router->get('/MItemOptDetail/{id}', ['uses' => 'MItemOptDetailController@showData']);
    // $router->post('/MItemOptDetail', ['uses' => 'MItemOptDetailController@storeData']);
    // $router->put('/MItemOptDetail/{id}', ['uses' => 'MItemOptDetailController@updateData']);
    // $router->delete('/MItemOptDetail/{id}', ['uses' => 'MItemOptDetailController@destroyData']);

    // // Routing untuk m_item ihza
    // $router->get('/m_item', ['uses' => 'MItemController@paging']); // Untuk pagination dan pencarian
    // $router->post('/m_item', ['uses' => 'MItemController@store']); // Menyimpan data baru
    // $router->get('/m_item/{id}', ['uses' => 'MItemController@show']); // Menampilkan detail data berdasarkan ID
    // $router->put('/m_item/{id}', ['uses' => 'MItemController@update']); // Memperbarui data berdasarkan ID
    // $router->delete('/m_item/{id}', ['uses' => 'MItemController@destroy']); // Menghapus data berdasarkan ID (soft delete)
    // $router->get('/m_itemAll', ['uses' => 'MItemController@getAllData']);

    // // Routing untuk m_item_inventory ihza
    // $router->get('/m_item_inventory', ['uses' => 'MItemInventoryController@paging']);
    // $router->post('/m_item_inventory', ['uses' => 'MItemInventoryController@store']); // Menyimpan data baru
    // $router->get('/m_item_inventory/{id}', ['uses' => 'MItemInventoryController@show']); // Menampilkan detail data berdasarkan ID
    // $router->put('/m_item_inventory/{id}', ['uses' => 'MItemInventoryController@update']); // Memperbarui data berdasarkan ID
    // $router->delete('/m_item_inventory/{id}', ['uses' => 'MItemInventoryController@destroy']); // Menghapus data berdasarkan ID (soft delete)

    // // API UNTUK table m_warehouse jonatan
    // $router->get('/M_Warehouse', ['uses' => 'M_WarehouseController@paging']);
    // $router->post('/M_Warehouse', ['uses' => 'M_WarehouseController@store']);
    // $router->put('/M_Warehouse/{id}', ['uses' => 'M_WarehouseController@update']);
    // $router->get('/M_Warehouse/{id}', ['uses' => 'M_WarehouseController@show']);
    // $router->delete('/M_Warehouse/{id}', ['uses' => 'M_WarehouseController@destroy']);

    // // API UNTUK table m_warehouse jonatan
    // $router->get('/M_Warehouse_Type', ['uses' => 'MwtController@paging']);
    // $router->post('/M_Warehouse_Type', ['uses' => 'MwtController@store']);
    // $router->put('/M_Warehouse_Type/{id}', ['uses' => 'MwtController@update']);
    // $router->get('/M_Warehouse_Type/{id}', ['uses' => 'MwtController@show']);
    // $router->delete('/M_Warehouse_Type/{id}', ['uses' => 'MwtController@destroy']);

    // //mitemprice khalaf
    // $router->get('/mitemprice', ['uses' => 'MitemPriceController@paging']);
    // $router->post('/mitemprice', ['uses' => 'MitemPriceController@store']);
    // $router->delete('/mitemprice/{id}', ['uses' => 'MitemPriceController@destroy']);
    // $router->put('mitemprice/{id}', ['uses' => 'MitemPriceController@update']);
    // $router->get('mitemprice/{id}', ['uses' => 'MitemPriceController@show']);

    // //mitemdiscont khalaf
    // $router->get('/mitemdiscount', ['uses' => 'MitemDiscountController@paging']);
    // $router->post('/mitemdiscount', ['uses' => 'MitemDiscountController@store']);
    // $router->delete('/mitemdiscount/{id}', ['uses' => 'MitemDiscountController@destroy']);
    // $router->put('mitemdiscount/{id}', ['uses' => 'MitemDiscountController@update']);
    // $router->get('mitemdiscount/{id}', ['uses' => 'MitemDiscountController@show']);

    //m_item_opt_setting amar
    // $router->get('/MItemoptsetting', ['uses' => 'MItemoptsettingController@paging']);
    // $router->post('/MItemoptsetting', ['uses' => 'MItemoptsettingController@store']);
    // $router->delete('/MItemoptsetting/{id}', ['uses' => 'MItemoptsettingController@destroy']);

    // $router->get('/MItemoptsetting/{id}', ['uses' => 'MItemoptsettingController@show']);
    // $router->put('/MItemoptsetting/{id}', ['uses' => 'MItemoptsettingController@update']);


    // // company putri
    // $router->get('company', 'MCompanyController@paging');
    // $router->post('company', 'MCompanyController@store');
    // $router->delete('company/{id}', 'MCompanyController@destroy');
    // $router->get('company/{id}', 'MCompanyController@show');
    // $router->put('company/{id}', 'MCompanyController@update');


    // // counter putri
    // $router->get('counter', 'CounterPreferenceController@paging');
    // $router->post('counter', 'CounterPreferenceController@store');
    // $router->delete('counter/{id}', 'CounterPreferenceController@destroy');
    // $router->get('counter/{id}', 'CounterPreferenceController@show');
    // $router->put('counter/{id}', 'CounterPreferenceController@update');

    // ROUTES STM
    // $router->get('stm', 'StmController@paging');
    // $router->post('stm', 'StmController@store');
    // $router->delete('stm/{id}', 'StmController@destroy');
    // $router->get('stm/{id}', 'StmController@show');
    // $router->put('stm/{id}', 'StmController@update');

    // // ROUTES STD
    // $router->get('std', 'StdController@paging');
    // $router->post('std', 'StdController@store');
    // $router->delete('std/{id}', 'StdController@destroy');
    // $router->get('std/{id}', 'StdController@show');
    // $router->put('std/{id}', 'StdController@update');

    // ROUTES BASOBA
    // $router->get('basoba', 'BasobaController@paging');
    // $router->post('basoba', 'BasobaController@store');
    // $router->delete('basoba/{id}', 'BasobaController@destroy');
    // $router->get('basoba/{id}', 'BasobaController@show');
    // $router->put('basoba/{id}', 'BasobaController@update');

    $router->post('stmBulky', 'StmController@storeBulky');
    $router->post('stdBulky', 'StdController@storeBulky');
    $router->post('basobaBulky', 'BasobaController@storeBulky');
    $router->post('trendBulky', 'TrendController@storeBulky');
    $router->post('pocustBulky', 'POCustController@storeBulky');
    $router->post('stockdetailBulky', 'StockDetailController@storeBulky');
    $router->post('salesunitBulky', 'SalesUnitController@storeBulky');
    $router->post('MItempricehistoryBulky', 'MItempricehistoryController@storeBulky');
    $router->post('M_BrandBulky', 'MBrandController@storeBulky');
    $router->post('M_AreaBulky', 'MAreaController@storeBulky');
    $router->post('M_RegionBulky', 'MRegionController@storeBulky');
    $router->post('M_CabangBulky', 'MCabangController@storeBulky');
    $router->post('M_OpsiCabangBulky', 'MOpsiCabangController@storeBulky');
    $router->post('M_ProductBulky', 'MProductController@storeBulky');
    $router->post('M_PriceBulky', 'MPriceController@storeBulky');


    $router->get('stmBulky', 'StmController@pagingBulky');
    $router->get('stdBulky', 'StdController@pagingBulky');
    $router->get('basobaBulky', 'BasobaController@pagingBulky');
    $router->get('trendBulky', 'TrendController@pagingBulky');
    $router->get('pocustBulky', 'POCustController@pagingBulky');
    $router->get('stockdetailBulky', 'StockDetailController@pagingBulky');
    $router->get('salesunitBulky', 'SalesUnitController@pagingBulky');
    $router->get('MItempricehistoryBulky', 'MItempricehistoryController@pagingBulky');

    $router->get('stmAll', ['uses' => 'StmController@getAllData']);
    $router->get('stdAll', ['uses' => 'StdController@getAllData']);
    $router->get('basobaAll', ['uses' => 'BasobaController@getAllData']);
    $router->get('trendAll', ['uses' => 'TrendController@getAllData']);
    $router->get('pocustAll', ['uses' => 'POCustController@getAllData']);
    $router->get('stockdetailAll', ['uses' => 'StockDetailController@getAllData']);
    $router->get('salesunitAll', ['uses' => 'SalesUnitController@getAllData']);

    $router->get('M_RegionAll', ['uses' => 'MRegionController@getAllData']);
    $router->get('ChannelAll', ['uses' => 'TrendController@getAllData']);
    $router->get('M_AreaAll', ['uses' => 'MAreaController@getAllData']);
    $router->get('M_CabangAll', ['uses' => 'MCabangController@getAllData']);
    $router->get('M_OpsiCabangAll', ['uses' => 'MOpsiCabangController@getAllData']);
    $router->get('M_CustomerAll', ['uses' => 'MCustomerController@getAllData']);
    $router->get('ProductAll', ['uses' => 'TrendController@getAllDatas']);

    $router->get('M_Areaget', ['uses' => 'MAreaController@getByData']);
    $router->get('TrendData', ['uses' => 'TrendController@getTrendData']);
    $router->get('cabangdistcode', ['uses' => 'MCabangController@getDataByDistCode']);

});
