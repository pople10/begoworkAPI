<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UserController;

Route::post('/api/user/signup', [UserController::class,"store"]);

Route::middleware('auth:sanctum')->post('/api/user/profile/change', [UserController::class,"edit"]);

Route::middleware('auth:sanctum')->get('/api/user/profile', [UserController::class,"getProfile"]);

use App\Http\Controllers\AccountController;

Route::post('/api/account/signup', [AccountController::class,"signUp"]);

Route::post('/api/account/login', [AccountController::class,"login"]);

Route::middleware('auth:sanctum')->delete('/api/account/delete', [AccountController::class,"delete"]);

Route::middleware('auth:sanctum')->get('/api/account/logout', [AccountController::class,"logout"]);

Route::middleware('auth:sanctum')->post('/api/account/change/password', [AccountController::class,"edit"]);

use App\Http\Controllers\LocationController;

Route::middleware('auth:sanctum')->get('/api/location/cities', [LocationController::class,"getDistinctCities"]);

Route::middleware('auth:sanctum')->get('/api/location/countries', [LocationController::class,"getDistinctCountries"]);

Route::middleware('auth:sanctum')->get('/api/location/{id}', [LocationController::class,"findOne"]);

use App\Http\Controllers\OrderController;

Route::middleware('auth:sanctum')->get('/api/order', [OrderController::class,"findAllByAuth"]);

Route::middleware('auth:sanctum')->get('/api/order/{ordID}', [OrderController::class,"findOneByAuth"]);

Route::middleware('auth:sanctum')->post('/api/order/reserve', [OrderController::class,"reserve"]);

use App\Http\Controllers\ListingTypeController;

Route::middleware('auth:sanctum')->get('/api/listing/type', [ListingTypeController::class,"findAll"]);

use App\Http\Controllers\ListingController;

Route::middleware('auth:sanctum')->get('/api/listing/search/type', [ListingController::class,"findByTypeID"]);

Route::middleware('auth:sanctum')->get('/api/listing/search/keyword', [ListingController::class,"findByKeyword"]);

Route::middleware('auth:sanctum')->get('/api/listing/search/city', [ListingController::class,"findByCity"]);

Route::middleware('auth:sanctum')->get('/api/listing/search/country', [ListingController::class,"findByCountry"]);

Route::middleware('auth:sanctum')->get('/api/listing/search/countryandcity', [ListingController::class,"findByCountryAndCity"]);

Route::middleware('auth:sanctum')->get('/api/listing/search/all', [ListingController::class,"findByAllCriteria"]);

Route::middleware('auth:sanctum')->get('/api/listing/{idListing}', [ListingController::class,"findOne"]);

Route::middleware('auth:sanctum')->get('/api/listing', [ListingController::class,"findAll"]);

use App\Http\Controllers\ProductController;

Route::middleware('auth:sanctum')->get('/api/listing/{idListing}/product', [ProductController::class,"getProductsByListingID"]);

use App\Http\Controllers\WorkingTimeController;

Route::middleware('auth:sanctum')->get('/api/listing/{idListing}/workingtime', [WorkingTimeController::class,"getWorkingTimeByListingID"]);