<?php

use App\Http\Controllers\BagController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('User.login');
});
Route::get("/test",function(){
    return view("Layout/test");
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(Dashboard::class)->group(function(){
        Route::get('/home',"home")->name("home");
    });
    Route::controller(MenuController::class)->group(function(){
        Route::get("/menu-list","getMenuList")->name("menu-list");
        Route::get("/menu-submenu","getSubMenuList")->name("submenu-list");
        Route::post("/menu-add","add")->name("menu-add");
        Route::get("/menu-edit/{id}","getMenuDtl")->name("menu-edit");
        Route::post("/menu-deactivate/{id}","deactivate")->name("menu-deactivate");
    });
    Route::controller(UserController::class)->group(function(){
        Route::match(["get","post"],"user/login","login")->name("login")->withoutMiddleware("auth:sanctum");
        Route::get("user/logout","logout")->name("logout");
        Route::match(["get","post"],"user/create","createUser")->name("createUser");
        Route::get("user/haspassword/{id?}","hasPassword")->name("haspassword");
        Route::get("user/profile","profile")->name("profile");
        Route::match(["get","post"],"user/change-password","changePassword")->name("change-password");
        Route::get("user/list","userList")->name("user.list");
        Route::get("user/{id}","userDtl")->name("user.id");
        Route::post("user/add","createUser")->name("user.add");
        Route::post("user/edit","editUser")->name("user.edit");
    });

    Route::controller(VendorController::class)->group(function(){
        Route::get("vender/list","vendorList")->name("vendor.list");
        Route::post("vender/add","addVendor")->name("vendor.add");
        Route::get("vender/edit/{id}","getVenderDtl")->name("vendor.edit");
        Route::post("vender/deactivate/{id}","deactivate")->name("vendor.deactivate");
    });

    Route::controller(ClientController::class)->group(function(){
        Route::get("client/list","clientList")->name("client.list");
        Route::post("client/add","addClient")->name("client.add");
        Route::get("client/edit/{id}","getClientDtl")->name("client.edit");
    });

    Route::controller(BagController::class)->group(function(){
        Route::get("bag/list","bagList")->name("bag.list");
        Route::post("bag/add","addBag")->name("bag.add");
        Route::get("bag/edit/{id}","getBagDtl")->name("bag.edit");
    });

    Route::controller(ColorController::class)->group(function(){
        Route::get("roll/color/list","rollColorList")->name("roll.color.list");
        Route::post("roll/color/add","addRollColor")->name("roll.color.add");
        Route::get("roll/color/edit/{id}","rollColorDtl")->name("roll.color.edit");

        Route::get("color/list","colorList")->name("color.list");
        Route::post("color/add","addColor")->name("color.add");
        Route::get("color/edit/{id}","colorDtl")->name("color.edit");
    });

    Route::controller(RollController::class)->group(function(){
        Route::get("roll/order/punches","orderPunches")->name("order.punches");
        Route::post("roll/order/punches/save","orderPunchesSave")->name("order.punches.save");
        Route::post("roll/client/old/order","oldOrderOfClient")->name("client.old.order");
        Route::post("roll/client/order/suggestion","orderSuggestionClient")->name("client.order.suggestion");

        Route::match(["get","post"],"roll/order/book","bookedOrder")->name("order.book");
        Route::match(["get","post"],"roll/order/unbook","unBookedOrder")->name("order.unbook");

        Route::get("roll/transit/list","transitList")->name("roll.transit");
        Route::get("roll/transit/dtl/{vendor_id?}","transitDtl")->name("roll.transit.dtl");
        Route::post("roll/transit/add","addTransitRoll")->name("roll.transit.add");
        Route::post("roll/transit/book","rollTransitBook")->name("roll.transit.book");
        Route::post("roll/transit/roll/stock","rollTransitRollStock")->name("roll.transit.rll.stock");

        Route::get("roll/list/{flag?}","rollList")->name("roll.list");
        Route::post("roll/add","addRoll")->name("roll.add");
        Route::post("roll/import","importRoll")->name("roll.import");
        Route::post("roll/book","rollBook")->name("roll.book");
        Route::get("roll/dtl/{id}","rollDtl")->name("roll.dtl");
        Route::post("roll/schedule/printing","rollPrintingSchedule")->name("roll.printing.schedule");
        Route::post("roll/printing/update","rollPrintingUpdate")->name("roll.printing.update");
        Route::post("roll/schedule/cutting","rollCuttingSchedule")->name("roll.cutting.schedule");
        Route::post("roll/cutting/update","rollCuttingUpdate")->name("roll.cutting.update");

        Route::get("roll/register","rollRegister")->name("roll.register");
        Route::get("roll/register/printing/{machineId?}","rollRegisterPrinting")->name("roll.register.printing");
        Route::get("roll/register/cutting/{machineId?}","rollRegisterCutting")->name("roll.register.cutting");
        Route::get("roll/schedule/{flag?}","rollSchedule")->name("roll.schedule");
        Route::post("roll/schedule-set/{flag}","rollScheduleSet")->name("roll.schedule.set");
        Route::get("roll/production/printing/{machineId?}","rollProduction")->name("roll.production.printing");
        Route::get("roll/production/cutting/{machineId?}","rollProductionCutting")->name("roll.production.cutting");

        Route::post("roll/search/printing","rollSearchPrinting")->name("roll.search.printing");
        Route::post("roll/search/cutting","rollSearchCutting")->name("roll.search.cutting");
    });

});
