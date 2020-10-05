<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home',                             'HomeController@index')->name('home');

Route::get('/wellbore/{userId?}',                  'WelboreController@index')->middleware('auth');

Route::get('/user-mmp',                         'UserMMPController@index')->middleware('auth');

Route::get('/justus-mmp',                       'UserMMPController@justus')->middleware('auth');

/*                   ADMIN AREA                  */
Route::get('/admin',                            'AdminController@index')->middleware('auth');

Route::get('/admin/updatePermits',              'AdminController@updatePermits')->middleware('auth');

/*                   LEASE CREATOR                 */
Route::get('/lease-creator',                            'LeaseCreatorController@index')->middleware('auth');

Route::post('/lease-creator',              'LeaseCreatorController@createLease')->middleware('auth')->name('createLease');

/*                  PERMIT STORAGE               */
Route::get('/permit-storage',                   'PermitStorageController@index')->middleware('auth');

Route::get('/permit-storage/sendBack',          'PermitStorageController@sendBack')->middleware('auth');

/*                  MMP PAGE                     */
Route::get('/mm-platform',                      'MMPController@index')->middleware('auth');

Route::get('/new-permits/getNotes',             'MMPController@getNotes')->middleware('auth');

Route::put('/new-permits/updateNotes',          'MMPController@updateNotes')->middleware('auth');

Route::post('/new-permits/updateStatus',        'MMPController@updateStatus')->middleware('auth');

Route::put('/new-permits/updateAssignee',       'MMPController@updateAssignee')->middleware('auth');

Route::put('/new-permits/stitchLeaseToPermit',  'MMPController@stitchLeaseToPermit')->middleware('auth');

Route::get('/new-permits/getPermitDetails',     'MMPController@getPermitDetails')->middleware('auth');

Route::post('/new-permits/delete/delete-note',  'MMPController@deleteNote')->middleware('auth');

Route::get('/new-permits/storePermit',          'MMPController@storePermit')->middleware('auth');

Route::put('/update-prices',                    'MMPController@updatePrices');

/*                                  LEASE/WELLBORE PAGE                        */
Route::get('/lease-page/{interestArea?}/{leaseName}/{isProducing?}/{permitId?}', 'LeasePageController@index')->middleware('auth');

Route::post('/lease-page/updateAcreage',        'LeasePageController@updateAcreage')->middleware('auth');

Route::post('/lease-page/updateLeaseNames',     'LeasePageController@updateLeaseNames')->middleware('auth');

Route::post('/lease-page/updateWellNames',      'LeasePageController@updateWellNames')->middleware('auth');

Route::put('/lease-page/updateAssignee',        'LeasePageController@updateAssignee')->middleware('auth');

Route::put('/lease-page/updateWellType',        'LeasePageController@updateWellType')->middleware('auth');

Route::put('/lease-page/updateFollowUp',        'LeasePageController@updateFollowUp')->middleware('auth');

Route::get('/lease-page/getOwnerInfo',          'LeasePageController@getOwnerInfo')->middleware('auth');

Route::post('/lease-page/update/OwnerPrice',    'LeasePageController@updateOwnerPrice')->middleware('auth');

Route::get('/lease-page/getWellDetails',        'LeasePageController@getWellInfo')->middleware('auth');

Route::get('/lease-page/getNotes',              'LeasePageController@getNotes')->middleware('auth');

Route::put('/lease-page/updateNotes',           'LeasePageController@updateNotes')->middleware('auth');

Route::post('/lease-page/delete-note',          'LeasePageController@deleteNote')->middleware('auth');

Route::get('/lease-page/getOwnerNumbers',       'LeasePageController@getOwnerNumbers')->middleware('auth');

Route::post('/lease-page/addPhone',             'LeasePageController@addPhone')->middleware('auth');

Route::put('/lease-page/pushPhoneNumber',       'LeasePageController@pushPhoneNumber')->middleware('auth');

Route::post('/lease-page/softDeletePhone',      'LeasePageController@softDeletePhone')->middleware('auth');

/*                  PUSHED PHONE NUMBER PAGE            */
Route::get('/pushed-phone-numbers',                     'PushedPhoneNumbersController@index')->middleware('auth');

Route::put('/pushed-phone-numbers/updatePhoneNumber',   'PushedPhoneNumbersController@updatePhoneNumber')->middleware('auth');

Route::post('/pushed-phone-numbers/insertPhoneNumber',  'PushedPhoneNumbersController@insertPhoneNumber')->middleware('auth');

/*                      OWNER PAGE                     */
Route::get('/owner/{interestArea?}/{ownerName?}/{isProducing?}',       'OwnersController@index')->middleware('auth');

Route::put('/owner/updateEmail',                        'OwnersController@updateEmail')->middleware('auth');